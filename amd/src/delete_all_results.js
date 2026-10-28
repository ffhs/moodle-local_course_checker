import ModalDeleteCancel from 'core/modal_delete_cancel';
import ModalEvents from 'core/modal_events';
import {getString} from 'core/str';

const SELECTORS = {
    trigger: '[data-action="delete-all-results"]',
    form: '[data-region="delete-all-results-form"]',
};

/**
 * Initialise the delete confirmation modal.
 */
export const init = () => {
    document.addEventListener('click', async (event) => {
        const trigger = event.target.closest(SELECTORS.trigger);
        if (!trigger) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const form = trigger.closest(SELECTORS.form);
        if (!form) {
            return;
        }

        const modal = await ModalDeleteCancel.create({
            title: getString('delete_all_results_title', 'local_course_checker'),
            body: getString('delete_all_results_confirm', 'local_course_checker'),
            buttons: {
                delete: getString('delete_all_results', 'local_course_checker'),
            },
            show: false,
            removeOnClose: true,
            returnElement: trigger,
        });

        modal.getRoot().on(ModalEvents.delete, (deleteEvent) => {
            deleteEvent.preventDefault();
            trigger.disabled = true;
            form.submit();
        });
        modal.show();
    });
};
