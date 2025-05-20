import { Fancybox } from '@fancyapps/ui';
import 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-scroller-dt';
import Select2 from 'select2';

jQuery(function($) {
    $('#myTable').DataTable({
        responsive: true,
        ordering: true,
        order: [],
        "info": false,
        language: {
            emptyTable: 'You have not assigned any courses.',
            zeroRecords: 'There is no record to display.'
        },
        pagingType: 'simple_numbers',
        "fnDrawCallback": function(oSettings) {
            if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
                $(oSettings.nTableWrapper).find('.dt-paging').hide();
            } else {
                $(oSettings.nTableWrapper).find('.dt-paging').show();
            }
        }
    });

    let debounceTimers = {};

    $('input[data-list]').on('input', function() {
        const $input = $(this);
        const listSelector = $input.data('list');
        const $listItems = $(`${listSelector} li`);
        const $noResults = $(`${listSelector}`).siblings('.no-results');

        clearTimeout(debounceTimers[listSelector]);

        debounceTimers[listSelector] = setTimeout(() => {
            const query = $input.val().toLowerCase();
            let visibleCount = 0;
            let visibleItems = [];

            $listItems.each(function() {
                const text = $(this).text().toLowerCase();
                const isVisible = text.includes(query);
                $(this).toggle(isVisible);
                if (isVisible) {
                    visibleCount++;
                    visibleItems.push($(this));
                }
            });

            $listItems.removeClass('last-item');

            if (visibleItems.length > 0) {
                visibleItems[visibleItems.length - 1].addClass('last-item');
            }


            if (visibleCount === 0) {
                $noResults.show();
                $(listSelector).hide()
            } else {
                $noResults.hide();
                $(listSelector).show()
            }
        }, 300);
    });

    function updateEmployeeCount() {
        const count = $('.stlms-employee:checked').length;
        $('#employee_cnt').text(count + ' Selected');

        if ( count > 1 ) {
            $('.stlms-switch-wrap').show();
        } else {
            $('.stlms-switch-wrap').hide();
        }
    }

    updateEmployeeCount();

    $(document).on('change', '.stlms-check', function() {
        updateEmployeeCount();
    });

    const $commonDateCheckbox = $('.stlms-switch-wrap .stlms-check');
    const $commonDateField = $('#common-date');
    const $uniqueDateContainer = $('#unique-date');
    const $employeeList = $('#employee-list');

    function updateDateFields() {
        const isCommon = $commonDateCheckbox.is(':checked');
        const $selectedEmployees = $employeeList.find('.stlms-check:checked');
        const today = new Date().toISOString().slice(0, 10)

        if (isCommon) {
            $commonDateField.show();
            $uniqueDateContainer.hide().empty();
        } else {
            $commonDateField.hide();
            $uniqueDateContainer.empty().show();

            $selectedEmployees.each(function() {
                const employeeName = $(this).closest('label').text().trim();

                const dateField = `
                    <div class="stlms-form-col">
                        <div class="stlms-form-group">
                            <label>Completion Date For ${employeeName}</label>
                            <input type="date" min="${today}" name="completion_date[${employeeName}]" />
                        </div>
                    </div>
                `;

                $uniqueDateContainer.append(dateField);
            });
        }
    }

    // Initial toggle.
    updateDateFields();

    // Toggle when common checkbox is clicked.
    $commonDateCheckbox.on('change', function() {
        updateDateFields();
    });

    // Update when any employee checkbox changes.
    $employeeList.on('change', '.stlms-check', function() {
        updateDateFields();
    });
});

Fancybox.bind('[data-fancybox]', {
    on: {
        ready: (fancybox) => {
            jQuery('.stmls-select2').each(function() {
                const $select = jQuery(this);
                const $modal = $select.closest('.stlms-dialog');

                if (!$select.hasClass('select2-hidden-accessible')) {
                    $select.select2({
                        dropdownParent: $modal
                    });
                }
            });

            jQuery('select').on('select2:open', function($) {
                $('.select2-search--dropdown .select2-search__field').attr('placeholder', 'Search here...');
            });
        }
    }
});

// Reset filter js
jQuery(function($) {
    $('.stlms-form-control').on('change', function () {
        const selectedValue = $(this).val();

        if (selectedValue) {
            $('.dt-input').val(selectedValue).trigger('input');
        } else {
            $('.dt-input').val('').trigger('input');
        }
    });

    $('.stlms-reset-btn').on('click', function (e) {
        e.preventDefault();
        $('.stlms-form-control').val('');
        $('.dt-input').val('').trigger('input');
    });
});


let snackbarTimeout;

function showSnackbar(snackbarId) {
    const $snackbar = jQuery('#' + snackbarId);
    $snackbar.addClass('show');

    clearTimeout(snackbarTimeout);

    snackbarTimeout = setTimeout(() => {
        $snackbar.removeClass('show');
    }, 3000);
}

// Hide snackbar on close button click
jQuery(document).on('click', '.hideSnackbar', function (e) {
    e.preventDefault();
    jQuery(this).closest('.stlms-snackbar').removeClass('show');
    clearTimeout(snackbarTimeout);
});

// Validate and show appropriate snackbar.
jQuery('#showSnackbar').on('click', function (e) {
    e.preventDefault();

    const $selectedCourse = jQuery('#course-list .stlms-check[type="radio"]:checked');
    const $selectedEmployees = jQuery('#employee-list .stlms-check[type="checkbox"]:checked');

    if ($selectedCourse.length > 0 && $selectedEmployees.length > 0) {
        const courseId = $selectedCourse.val();
        const assignCourseData = [];

        const isCommon = jQuery('.stlms-switch-wrap input[type="checkbox"]').is(':checked');
        const commonDate = jQuery('#common-date input[type="date"]').val();

        // Loop through selected employees.
        $selectedEmployees.each(function (index) {
            const encodedId = jQuery(this).val();
            let decodedId = '';
            try {
                decodedId = atob(encodedId);
            } catch (err) {
                showSnackbar('snackbar-error');
            }

            let completionDate = '';

            if (isCommon) {
                completionDate = commonDate;
            } else {
                const $uniqueDateInputs = jQuery('#unique-date input[type="date"]');
                if ($uniqueDateInputs.length > index) {
                    completionDate = $uniqueDateInputs.eq(index).val();
                }
            }

            assignCourseData.push({
                course_id: parseInt(courseId),
                user_id: parseInt(decodedId),
                completion_date: completionDate
            });
        });

        jQuery.post(
            StlmsObject.ajaxurl,
            {
                action: 'assign_new_course',
                _nonce: StlmsObject.nonce,
                assign_course_data: assignCourseData,
            },
            function (response) {
                showSnackbar('snackbar-success');
                setTimeout(function () {
                    window.location.href = StlmsObject.assignCourseUrl;
                }, 500);
            }
        );
    } else {
        showSnackbar('snackbar-error');
    }
});