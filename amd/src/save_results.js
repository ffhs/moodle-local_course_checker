import ModalSaveCancel from 'core/modal_save_cancel';
import ModalEvents from 'core/modal_events';
import Templates from 'core/templates';
import {getString} from 'core/str';

const SELECTORS = {
    trigger: '[data-action="save-results"]',
    form: '[data-region="save-results-form"]',
    versionName: '[data-region="version-name"]',
    submittedVersionName: 'input[name="version_name"]',
    overwrite: 'input[name="overwrite"]',
    existingVersionName: '[data-existing-version-name]',
};

/**
 * Submit the save-results form.
 *
 * @param {HTMLFormElement} form
 * @param {HTMLButtonElement} trigger
 * @param {string} versionName
 * @param {boolean} overwrite
 */
const submitForm = (form, trigger, versionName, overwrite) => {
    form.querySelector(SELECTORS.submittedVersionName).value = versionName;
    form.querySelector(SELECTORS.overwrite).value = overwrite ? '1' : '0';
    trigger.disabled = true;
    form.submit();
};

/**
 * Ask the user to confirm overwriting an existing version.
 *
 * @param {HTMLFormElement} form
 * @param {HTMLButtonElement} trigger
 * @param {string} versionName
 */
const confirmOverwrite = async(form, trigger, versionName) => {
    const modal = await ModalSaveCancel.create({
        title: getString('overwrite_results_title', 'local_course_checker'),
        body: getString('overwrite_results_confirm', 'local_course_checker', versionName),
        buttons: {
            save: getString('overwrite_results', 'local_course_checker'),
        },
        show: true,
        removeOnClose: true,
        returnElement: trigger,
    });

    modal.getFooter().find('[data-action="save"]')
        .removeClass('btn-primary')
        .addClass('btn-warning');
    modal.getRoot().on(ModalEvents.save, () => submitForm(form, trigger, versionName, true));
};

/**
 * Ask the user to replace the oldest saved report after reaching the configured limit.
 *
 * @param {HTMLFormElement} form
 * @param {HTMLButtonElement} trigger
 * @param {string} versionName
 * @param {string} oldestVersionName
 */
const confirmReplaceOldest = async(form, trigger, versionName, oldestVersionName) => {
    const modal = await ModalSaveCancel.create({
        title: getString('replace_oldest_report_title', 'local_course_checker'),
        body: getString('replace_oldest_report_confirm', 'local_course_checker', oldestVersionName),
        buttons: {
            save: getString('replace_oldest_report', 'local_course_checker'),
        },
        show: true,
        removeOnClose: true,
        returnElement: trigger,
    });

    modal.getFooter().find('[data-action="save"]')
        .removeClass('btn-primary')
        .addClass('btn-warning');
    modal.getRoot().on(ModalEvents.save, () => submitForm(form, trigger, versionName, true));
};

/**
 * Initialise the save-results modal.
 */
export const init = () => {
    document.addEventListener('click', async(event) => {
        const trigger = event.target.closest(SELECTORS.trigger);
        if (!trigger) {
            return;
        }

        event.preventDefault();

        const form = trigger.closest(SELECTORS.form);
        if (!form) {
            return;
        }

        const modal = await ModalSaveCancel.create({
            title: getString('save_results_title', 'local_course_checker'),
            body: Templates.render('local_course_checker/save_results_modal', {
                version_name: trigger.dataset.defaultVersionName || '',
            }),
            buttons: {
                save: getString('save_results', 'local_course_checker'),
            },
            show: false,
            removeOnClose: true,
            returnElement: trigger,
        });

        modal.getRoot().on('keydown', SELECTORS.versionName, (keyEvent) => {
            if (keyEvent.key !== 'Enter' || keyEvent.isComposing) {
                return;
            }

            keyEvent.preventDefault();
            modal.getRoot().trigger(ModalEvents.save);
        });

        modal.getRoot().on(ModalEvents.save, async(saveEvent) => {
            saveEvent.preventDefault();
            const input = modal.getRoot()[0].querySelector(SELECTORS.versionName);
            const versionName = input.value.trim();

            if (!input.checkValidity() || !versionName) {
                if (!versionName) {
                    input.setCustomValidity(await getString('version_name_required', 'local_course_checker'));
                }
                input.reportValidity();
                input.addEventListener('input', () => input.setCustomValidity(''), {once: true});
                return;
            }

            const existingVersionNames = [...form.querySelectorAll(SELECTORS.existingVersionName)]
                .map((element) => element.value.toLocaleLowerCase());
            if (existingVersionNames.includes(versionName.toLocaleLowerCase())) {
                modal.hide();
                await confirmOverwrite(form, trigger, versionName);
                return;
            }

            const maxSavedReports = Number.parseInt(form.dataset.maxSavedReports, 10) || 0;
            const savedReportCount = Number.parseInt(form.dataset.savedReportCount, 10) || 0;
            const selectedVersion = form.elements.selected_version.value;
            if (
                selectedVersion === 'latest' &&
                maxSavedReports > 0 &&
                savedReportCount >= maxSavedReports
            ) {
                modal.hide();
                await confirmReplaceOldest(form, trigger, versionName, form.dataset.oldestSavedVersion);
                return;
            }

            submitForm(form, trigger, versionName, false);
        });

        await modal.getBodyPromise();
        modal.getRoot().on(ModalEvents.shown, () => {
            const input = modal.getRoot()[0].querySelector(SELECTORS.versionName);
            input.focus();
            input.select();
        });
        modal.show();
    });
};
