$(document).ready(function () {
    PreyLang.init()
    PreyLang.hideShowVillagerField()
    PreyLang.showHidePassword()
    PreyLang.showRecordsPerPage()
    PreyLang.searchByKeyword()
    PreyLang.disableFields()
    PreyLang.processImpact()
    PreyLang.changeCategories()
    PreyLang.setDateTimeFormat()
    PreyLang.customizeSelect()
    PreyLang.checkRole()
})

var PreyLang = {
    formFields: [
        'category',
        'sub_category_1',
        'sub_category_2',
        'sub_category_3',
        'sub_category_4',
        'sub_category_5'
    ],
    lblShowFreeText: '',
    lblHideFreeText: '',
    impactFormSelector: '#impact-form-fields',
    $impactForm: null,
    init: function () {
        PreyLang.$impactForm = $(PreyLang.impactFormSelector)
    },
    disableFields: function () {
        $('.read-only select, .read-only input').attr('disabled', 'disabled')
    },
    customizeSelect: function () {
        $('.selectize').selectize({
            plugins: ['remove_button']
        })
    },
    hideShowVillagerField: function () {
        var villager = $('#villager_id')
        var role = $('#role')
        role.on('change', function () {
            var selectedRole = $('#role option:selected').val()
            if (selectedRole === role.attr('data-villager-role')) {
                $('#villager-block').removeClass('d-none')
                villager.attr('required', 'required')
            } else {
                $('#villager-block').addClass('d-none')
                villager.val('')
                villager.removeAttr('required')
            }
        })
    },
    showHidePassword: function () {
        $('input:checkbox.toggle-password').on('click', function () {
            var inputType = 'password'
            if ($(this).is(':checked')) {
                inputType = 'text'
            }
            var pwdElem = $('#password-id-' + $(this).data('password-id'))
            if (pwdElem.length) {
                pwdElem.attr('type', inputType)
                if (pwdElem.data('placeholder')) {
                    var pwdPlaceholder = pwdElem.data('placeholder')
                    if (inputType !== 'text') {
                        pwdPlaceholder = '******'
                    }
                    pwdElem.attr('placeholder', pwdPlaceholder)
                }
            }
        })
    },
    showRecordsPerPage: function () {
        $('#record-per-page').on('change', function () {
            var urlString = window.location.href
            var url = new URL(urlString)
            var fields = ['keyword', 'sort', 'direction']
            fields.forEach(function (field) {
                if (url.searchParams.get(field)) {
                    $(this)
                        .append('<input type="hidden" name="' + field +
                            '" value="' + url.searchParams.get(field) + '" />')
                }
            }.bind(this))
            $(this).append('<input type="hidden" name="page" value="1" />')
            this.form.submit()
        })
    },
    searchByKeyword: function () {
        $('#form-search').submit(function () {
            var urlString = window.location.href
            var url = new URL(urlString)
            var fields = ['perpage', 'sort', 'direction']
            fields.forEach(function (field) {
                if (url.searchParams.get(field)) {
                    $(this)
                        .append('<input type="hidden" name="' + field +
                            '" value="' + url.searchParams.get(field) + '" />')
                }
            }.bind(this))
            $(this).append('<input type="hidden" name="page" value="1" />')
            return true
        })
    },
    processImpact: function () {
        $('#chkExcluded').on('change', function () {
            if ($(this).is(':checked')) {
                $('#wrapExcludedReason').removeClass('hidden')
            } else {
                $('#wrapExcludedReason').addClass('hidden')
            }
        })

        $('#btn-impact-process').on('click', function () {
            var msg = $(this).attr('data-msg-error')
            var msgConfirm = $(this).attr('data-msg-confirm')

            var selected = []
            var action = $('#select-impact-action').val()
            $('.impact-list .chk-impact:checked').each(function () {
                if ($(this).is(':checked')) {
                    selected.push($(this).attr('data'))
                }
            })

            if (selected.length <= 0) {
                alert(msg)
            } else {
                var yes = confirm(msgConfirm)
                if (yes) {
                    switch (action) {
                        case 'exclude':
                        case 'include':
                            $.ajax({
                                url: '/impact/process',
                                type: 'GET',
                                data: {
                                    action: action,
                                    ids: selected
                                }
                            }).done(function (data) {
                                $('#form-impact-filter').submit()
                            })
                            break
                    }
                }
            }
        })

        $('#chk-all-item').on('change', function () {
            if ($(this).is(':checked')) {
                $('.impact-list .chk-impact').each(function () {
                    $(this).prop('checked', true)
                })
            } else {
                $('.impact-list .chk-impact').each(function () {
                    $(this).prop('checked', false)
                })
            }
        })
    },
    changeCategories: function () {
        var impactSearchForm = $('form[name="searchForm"]')
        if (impactSearchForm) {
            impactSearchForm.find('select[name="category"]').on('change', function () {
                PreyLang.updateSubCategoryList(0, $(this).val(), impactSearchForm, 'sub_category_1', true)
            })
        }

        var impactFormFields = $('#impact-form-fields')
        if (impactFormFields) {
            var otherCategories = PreyLang.getOptionCategories(impactFormFields, 'other-categories')
            var category = impactFormFields.find('select[name="' + PreyLang.formFields[0] + '"]')
            PreyLang.toggleDisplayReportingFields(impactFormFields)

            for (var i = 0; i < PreyLang.formFields.length; i++) {
                PreyLang.generateOnChangeCategory(impactFormFields, i)
                PreyLang.generateToggleFreeText(impactFormFields, PreyLang.formFields[i])

                // Disable subcategories on page load when empty and on detail page.
                var subcategorySelectbox = impactFormFields.find('select[name="' + PreyLang.formFields[i] + '"]')
                if (!subcategorySelectbox.find('option').length && !otherCategories.includes(category.val())) {
                    subcategorySelectbox.attr('disabled', 'disabled')
                }
            }

            // Show Toggle Free Text button when on other Category.
            if (otherCategories.includes(category.val())) {
                $('.add-category').removeClass('hidden')
            }
        }
    },
    generateToggleFreeText: function (formObj, elementName, showSelect) {
        $('#add-' + elementName).click(function () {
            PreyLang.toggleFreeText(formObj, elementName, showSelect)
        })
    },
    toggleFreeText: function (formObj, elementName, showSelect) {
        var freeText = $('#text-' + elementName)
        var selectBox = formObj.find('select[name="' + elementName + '"]')
        PreyLang.lblShowFreeText = selectBox.parent().find('.show-free-text')
        PreyLang.lblHideFreeText = selectBox.parent().find('.hide-free-text')

        // Show free text
        if ((freeText && freeText.hasClass('hidden')) && showSelect !== true) {
            PreyLang.lblShowFreeText.hide()
            PreyLang.lblHideFreeText.show()
            freeText.removeClass('hidden')
            freeText.removeAttr('disabled')
            PreyLang.clearSubCategories(elementName)
            PreyLang.showParentFreeTextWhenEmpty(elementName)
            freeText.focus()
            freeText.attr('required', true)
            selectBox.addClass('hidden')
        } else { // Hide free text
            PreyLang.lblShowFreeText.show()
            PreyLang.lblHideFreeText.hide()
            freeText.addClass('hidden')
            freeText.attr('disabled', 'disabled')
            freeText.attr('required', false)
            selectBox.removeClass('hidden')
        }
    },
    clearSubCategories: function (categorySelectBoxName) {
        var mainCategory = PreyLang.$impactForm.find('select[name="' + PreyLang.formFields[0] + '"] option:selected')
        var mainCategoryText = mainCategory.text()
        if ((mainCategoryText !== 'Other') && (categorySelectBoxName === PreyLang.formFields[3])) {
            return
        }

        var subCategoriesIndex = PreyLang.formFields.indexOf(categorySelectBoxName) + 1
        var categorySelectBox = PreyLang.$impactForm.find('select[name="' + categorySelectBoxName + '"]')
        categorySelectBox.val('')

        for (var i = subCategoriesIndex; i < PreyLang.formFields.length; i++) {
            var subcategorySelectbox = PreyLang.$impactForm.find('select[name="' + PreyLang.formFields[i] + '"]')
            subcategorySelectbox.empty()
        }
    },

    showParentFreeTextWhenEmpty: function (categorySelectBoxName) {
        for (var i = 0; i < PreyLang.formFields.length; i++) {
            var subcategorySelectbox = PreyLang.$impactForm.find('select[name="' + PreyLang.formFields[i] + '"]')
            var subcategoryFreeTextButton = subcategorySelectbox.siblings('.add-category')
            if (categorySelectBoxName === PreyLang.formFields[i]) {
                break
            }
            if (!subcategorySelectbox.find('option').length && !subcategorySelectbox.hasClass('hidden')) {
                subcategoryFreeTextButton.click()
            }
        }
    },
    generateOnChangeCategory: function (formObj, level) {
        formObj.find('select[name="' + PreyLang.formFields[level] + '"]').on('change', function () {
            var allowFreeText = $('#add-' + PreyLang.formFields[level])
            var otherCategories = PreyLang.getOptionCategories(formObj, 'other-categories')
            var categorySelectBoxValue = $('select[name="category"]').val()
            if (!allowFreeText.length || (allowFreeText && allowFreeText.hasClass('hidden')) || otherCategories.includes(categorySelectBoxValue)) {
                PreyLang.renderSubCategories(formObj, level)
                PreyLang.toggleDisplayReportingFields(formObj)
            }
        })
    },
    renderSubCategories: function (formObj, level) {
        var parentObj = formObj.find('select[name="' + PreyLang.formFields[level] + '"]')
        var elementId = PreyLang.formFields[level + 1]
        PreyLang.updateSubCategoryList(level, parentObj.val(), formObj, elementId)
    },
    updateSubCategoryList: function (level, parentVal, formObj, elementId, keepFirst) {
        if (level > PreyLang.formFields.length) {
            return
        }

        var otherCategories = PreyLang.getOptionCategories(formObj, 'other-categories')
        var category = formObj.find('select[name="' + PreyLang.formFields[0] + '"]')

        // Show Toggle Free Text button when on other Category
        if (otherCategories.includes(category.val())) {
            $('.add-category').removeClass('hidden')
        }

        // Refresh toggle free text to display select box first.
        PreyLang.toggleFreeText(formObj, PreyLang.formFields[level], true)

        var subcategorySelectbox = formObj.find('select[name="' + elementId + '"]')
        if (subcategorySelectbox.length === 0) {
            return
        }

        PreyLang.removeOptions(subcategorySelectbox, keepFirst)
        if (!parentVal) {
            return
        }

        $.ajax({
            url: '/impact/subcategories/' + level + '/' + parentVal,
            method: 'GET'
        }).done(function (result) {
            var subCategories = result.children
            var allowModify = result.modify_child
            var addCategoryButton = $('#add-' + elementId)

            // Disable select when no sub categories except for other.
            if (allowModify) {
                var previousSubCategory = $('#add-' + PreyLang.formFields[level])
                if (previousSubCategory && previousSubCategory.hasClass('hidden')) {
                    addCategoryButton.removeClass('hidden')
                }
                subcategorySelectbox.removeAttr('disabled')
            } else {
                if ($.isEmptyObject(subCategories) && !otherCategories.includes(category.val()) && !keepFirst) {
                    subcategorySelectbox.attr('disabled', 'disabled')
                } else {
                    subcategorySelectbox.removeAttr('disabled')
                }
                if (!addCategoryButton.hasClass('hidden')) {
                    addCategoryButton.addClass('hidden')
                }
            }

            // Append options of sub categories to select.
            var currentParentVal = Object.keys(subCategories)[0]
            $.each(subCategories, function (key, value) {
                subcategorySelectbox.append($('<option></option>').attr('value', key).text(value))
            })
            level += 1

            // Set default value for sub category 4 and 5 when Logging is selected in sub category 1.
            var loggingCategories = PreyLang.getOptionCategories(formObj, 'logging-categories')
            var subCategory1 = formObj.find('select[name="' + PreyLang.formFields[1] + '"]')
            if (subCategory1 && loggingCategories.includes(subCategory1.val())) {
                var dontKnowCategories = PreyLang.getOptionCategories(formObj, 'dontknow-categories')
                var interactionNoCategories = PreyLang.getOptionCategories(formObj, 'interactionno-categories')

                if (level === 4) {
                    $.each(dontKnowCategories, function (index, id) {
                        var option = subcategorySelectbox.find('option[value="' + id + '"]')
                        if (option.length) {
                            currentParentVal = id
                            option.prop('selected', true)
                        }
                    })
                }
                if (level === 5) {
                    $.each(interactionNoCategories, function (index, id) {
                        var option = subcategorySelectbox.find('option[value="' + id + '"]')
                        if (option.length) {
                            currentParentVal = id
                            option.prop('selected', true)
                        }
                    })
                }
            }

            // When skipping one level.
            currentParentVal = allowModify ? parentVal : currentParentVal

            // Recursive to refresh dependent sub category lists.
            PreyLang.updateSubCategoryList(
                level,
                currentParentVal || true, // empty select if no parent category
                formObj,
                PreyLang.formFields[level + 1],
                keepFirst
            )
        }).fail(function () {
            console.log('An error occurred')
        })
    },
    removeOptions: function (obj, keepFirst) {
        if (keepFirst) {
            obj.find('option:first-child')
                .siblings()
                .remove()
        } else {
            obj.find('option').remove()
        }
    },
    setDateTimeFormat: function () {
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        })

        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        })

        $('form[name="searchForm"] .datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        })
    },
    toggleDisplayReportingFields: function (formObj) {
        var subCategory1 = formObj.find('select[name="sub_category_1"]').val()
        var reportingCategories = PreyLang.getOptionCategories(formObj, 'reporting-categories')
        if (reportingCategories.includes(subCategory1)) {
            formObj.find('.reporting-fields').show()
        } else {
            formObj.find('.reporting-fields').hide()
        }
    },
    getOptionCategories: function (formObj, name) {
        var categories = formObj.find('input[name="' + name + '"]').val()
        if (!categories) {
            return []
        }
        return categories.split(',')
    },
    checkRole: function () {
        var role = $('#role')
        var selectedRole = role.val()
        PreyLang.disableUserGroupSelect(selectedRole)
        role.on('change', function () {
            var selectedRole = $(this).val()
            PreyLang.disableUserGroupSelect(selectedRole)
        })
    },
    disableUserGroupSelect: function (selectedRole) {
        if (selectedRole === superAdmin || selectedRole === superDataManager) {
            $('#user_group_id').attr('disabled', true).val('')
        } else {
            $('#user_group_id').attr('disabled', false)
        }
    }
}
