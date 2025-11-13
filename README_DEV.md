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

Inside the `checker` class, you can use the `checker_config_trait` to help read your subplugin settings. Then instead of `get_config` you would use `$this->get_config`. You would only need to specify the full path to the setting, like `coursechecker_links/timeout`.

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
class curl_manager {
    use checker_config_trait;
	/** @var string Configuration path for the URL request timeout. */
    const TIMEOUT_SETTING = 'coursechecker_links/timeout';
    /** @var int Default value for total CURL request timeout in seconds. */
    const TIMEOUT_DEFAULT = 13;

	public function __construct(check $check) {
        $this->connecttimeout = (int) $this->get_config(self::CONNECT_TIMEOUT_SETTING, self::CONNECT_TIMEOUT_DEFAULT);
```

## Save your results

The run function also passes `check $check`. This parameter is used to save the result into the database.
With the following function inside the `local_course_checker\db\model\check` class you can add results to the database.
* `public function add_error(string $title, string $link, string $message): check_result`
* `public function add_failed(string $title, string $link, string $message): check_result`
* `public function add_warning(string $title, string $link, string $message): check_result`
* `public function add_successful(string $title, string $link, string $message): check_result`

These functions save the result with the corresponding status. The difference between failed and error is that failed is for failed checks, and error should be used when there is a code exception.

In theory, you could also add your own custom status with the help of `public function add_result(string $status, string $title, string $link, string $message): check_result`. But then you would need to modify the mustache template, CSS, and other code parts of the main plugin, since currently it wouldn't display your custom results correctly.

## Make your result translateable

The `string $title` and `string $message` of the `public function add_` function allows you to pass strings to save into the database. Strings saved in the database are usually not translatable. You can add plain strings, but then they won't be translated when viewed in other languages.

If you wish to make the reports translatable, you can use `local_course_checker/translation_manager`. This class saves identifier, component, a and lazyload (used by `get_string` function) as a JSON into the database instead of a a plain string. Then, when opening the result page it would recursivly resolve the JSON to a string. This is done recursively because then you could add a translation inside a translation. You could also just append a json to another json. If the string is not a valid JSON it would return the value that is saved in the database as string.

This is how it could look like:
```php
$title = translation_manager::generate('admin_setting_coursesregex_skip_course', 'local_course_checker');
$message = translation_manager::generate('admin_setting_coursesregex_skip_course_desc', 'local_course_checker');
$this->check->add_successful($title, '', $message);
```

## Adhoc Tasks

- Checks are prepared and planed by `local/course_checker/classes/task/queue_check_task.php`.
  - Allows checkers to use less Database queries.
  - Checkers are able to run at the same time.
  - If one checks throws an exception it doesn't block the rest.
- The checks are executed by `local/course_checker/classes/task/run_checker.php`
  - If a check fails consistently and somehow blocks your moodle or creates other errors, you can disable the checker in the settings of the main plugin and let the failed adhoc_task run. The task would then end successfully because each run_checker would check if the subplugin is enabled at the beginning of the task.
- The notification is sent by `local/course_checker/classes/task/send_notification.php`
  - Notifies the user when the triggered check/checks is done.
  - Send an error if a check throws an exception or takes too long to execute.
---
