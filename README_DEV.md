# ⚙️ For Developers

Each [subplugin](https://docs.moodle.org/dev/Subplugins) should:

* Should be a working [subplugin](https://docs.moodle.org/dev/Subplugins).
* Be located under `local/course_checker/checker/<name>/`.
* Have `local/course_checker/checker/<name>/classes/checker.php` that implements `check_plugin_interface`.
* Have a language file with `$string['pluginname']` and `$string['pluginname_help']`.

---

## How to write a checker

Each checker is a subplugin (`local/course_checker/checker`) implementing the `check_plugin_interface`:
```php
interface check_plugin_interface {
    public function run(stdClass $course, check $check): void;
}
```
Example checker class:
```php
namespace checker_quiz;

class checker implements check_plugin_interface {
    public function run(\stdClass $course, check $check): void {
        // Logic for quiz checking
    }
}
```

With the help of `stdClass $course` of the run function, you can run your check and request more course info if needed. (Example: compare course content, test strings and so on.)

---

## Read your settings

Use `local_course_checker\config::get()` to read a subplugin setting. Pass the full configuration name in the form `plugin/setting`, for example `coursechecker_links/timeout`, and optionally provide a default value to return when the setting has not been stored.

The `config` class also provides additional configuration operations, such as `config::set()` for storing a value and `config::reset()` for removing a stored value so that its default can be used again. See `local_course_checker\config` for all available options.

Example:
```php
// settings.php
$visiblename = get_string('connect_timeout_setting', 'coursechecker_links');
$description = get_string('connect_timeout_setting_desc', 'coursechecker_links');
$timeout = new admin_setting_restrictedint(curl_manager::CONNECT_TIMEOUT_SETTING,
        $visiblename, $description, curl_manager::CONNECT_TIMEOUT_DEFAULT);
$timeout->set_maximum(300)->set_minimum(0);
$settings->add($timeout);

// checker.php (or curl_manager.php in this example)
use local_course_checker\config;

class curl_manager {
	/** @var string Configuration path for the URL request timeout. */
    const TIMEOUT_SETTING = 'coursechecker_links/timeout';
    /** @var int Default value for total CURL request timeout in seconds. */
    const TIMEOUT_DEFAULT = 13;

	public function __construct(check $check) {
        $this->connecttimeout = (int) config::get(self::CONNECT_TIMEOUT_SETTING, self::CONNECT_TIMEOUT_DEFAULT);
```

## Save your results

The run function also passes `check $check`. This parameter is used to save the result into the database.
With the following function inside the `local_course_checker\db\model\check` class you can add results to the database.
* `public function add_error(string $title, string $link, string $message, ?array $customdata = null): check_result`
* `public function add_failed(string $title, string $link, string $message, ?array $customdata = null): check_result`
* `public function add_warning(string $title, string $link, string $message, ?array $customdata = null): check_result`
* `public function add_info(string $title, string $link, string $message, ?array $customdata = null): check_result`
* `public function add_successful(string $title, string $link, string $message, ?array $customdata = null): check_result`

These functions save the result with the corresponding status. The difference between failed and error is that failed is for failed checks, and error should be used when there is a code exception. The difference between warning and info is that warning is for when a manual check is needed and info is for when a check is skipped for some reason.
The keys in the `$customdata` array must not conflict with the model's existing property names.

In theory, you could also add your own custom status with the help of `public function add_result( string $status, string $title, string $link, string $message, ?array $customdata = null): check_result`. But then you would need to modify the mustache template, CSS, and other code parts of the main plugin, since currently it wouldn't display your custom results correctly.

## Make your result translateable

The `string $title` and `string $message` of the `public function add_` function allows you to pass strings to save into the database. Strings saved in the database are usually not translatable. You can add plain strings, but then they won't be translated when viewed in other languages.

If you wish to make the reports translatable, you can use `local_course_checker/translation_manager`. This class saves identifier, component, a and lazyload (used by `get_string` function) as a JSON into the database instead of a a plain string. Then, when opening the result page it would recursivly resolve the JSON to a string. This is done recursively because then you could add a translation inside a translation. If the string is not a valid JSON it would return the value that is saved in the database as string.
If you need to conact, append or prepend a string to a translation, you can use the function in the translation_manager class to do so. The concatinated string can be itself a translation.

This is how it could look like:
```php
$title = translation_manager::generate('admin_setting_coursesregex_skip_course', 'local_course_checker');
$message = translation_manager::generate('admin_setting_coursesregex_skip_course_desc', 'local_course_checker');
$this->check->add_successful($title, '', $message);
```

## Custom data and custom templates/renderers

Subplugins can store additional data on a check or an individual check result. This data is JSON-encoded in the
`custom_data` database field and is automatically added to the data available to the corresponding Mustache template.

To add custom data to a result, pass an associative array as the optional final argument of any `add_*()` method:

```php
$customdata = [
    'external_link' => $url,
    'filetype' => 'pdf',
];
$this->check->add_successful($title, $link, $message, $customdata);
```

The keys can then be used directly by the result template:

```mustache
{{#external_link}}
    <a href="{{external_link}}">{{external_link}}</a>
{{/external_link}}
```

Custom data can also be stored on the check model with `$check->set('custom_data', $customdata)`. Remember to persist
the model with `$check->update()` when modifying an existing record. Custom-data keys must not use names already
defined by the `check` or `check_result` model. Core template data takes precedence if names overlap later during
rendering.

To override the default templates, add either of the following files to the subplugin:

* `local/course_checker/checker/<name>/templates/check.mustache`
* `local/course_checker/checker/<name>/templates/check_result.mustache`

To enrich the exported data before rendering, extend the corresponding core output class in:

* `local/course_checker/checker/<name>/classes/output/check.php`
* `local/course_checker/checker/<name>/classes/output/check_result.php`

For example, the link checker stores `filetype` in a result's custom data. Its custom `check_result` renderer converts
that value into `filetypeicon`, which its overridden `check_result.mustache` template displays. See
`checker/links/classes/output/check_result.php` and `checker/links/templates/check_result.mustache` for a complete
implementation.

## Adhoc Tasks

- Checks are prepared and planed by `local/course_checker/classes/task/queue_check_task.php`.
  - Allows checkers to use less Database queries.
  - Checkers are able to run at the same time.
  - If one checks throws an exception it doesn't block the rest.
- The checks are executed by `local/course_checker/classes/task/run_checker.php`
  - If a check fails consistently and somehow blocks your moodle or creates other errors, you can disable the checker in the settings of the main plugin and let the failed adhoc_task run. The task would then end successfully because each run_checker would check if the subplugin is enabled at the beginning of the task.
- The notification is sent by `local/course_checker/classes/task/send_notification.php`
  - Notifies the user when the triggered check/checks is done.
  - Send an error if a check throws an exception.

## Add your subplugin

- Have you created a subplugin others can benefit from?
  - Feel free to integrate it by linking it into the table of the [README.md](README.md) via a merge request.
---
