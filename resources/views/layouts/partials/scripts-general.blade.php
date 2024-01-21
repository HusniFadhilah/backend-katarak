<script>
    $(document).ready(function() {
        $('#datatable,.datatable').DataTable({});
        $('#select2,.select2').select2({});
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        $('.select2bs4-70').select2({
            theme: 'bootstrap4'
            , width: '70%'
        })
        $('#select2multiple,.select2multiple').select2({
            theme: 'default'
            , multiple: true
        });
        $('#select2multiplebs4,.select2multiplebs4').select2({
            theme: 'bootstrap4'
            , multiple: true
        });
        $('#buttonexportpdf,#buttonexportexcel').on("click", function(e) {
            e.preventDefault();
            let id = $(this).data('idform');
            //(id == 'exportexcel' || id == 'exportexcelforimport') ? $(this).parent().parent().parent().attr('id', id): $(this).parent().attr('id', id);
            let form = document.getElementById(id);
            let href = $(this).attr('href');
            form.action = href;
            form.submit();
        });
    });

    function getDataSelect2Search(id, url, placeholder = '- Pilih -', minimumInputLength = 2, isMultiple = true, dataParams = {}, isReverse = false) {
        $(function() {
            $("#" + id).select2({
                theme: isMultiple ? 'default' : 'bootstrap4'
                , minimumInputLength: minimumInputLength
                , placeholder: placeholder
                , multiple: isMultiple
                , allowClear: true
                , ajax: {
                    url: url
                    , type: "GET"
                    , data: function(params) {
                        let data = {
                            name: params.term
                        };
                        return {
                            ...data
                            , ...dataParams
                        }
                    }
                    , processResults: function(data) {
                        return {
                            results: $.map(data, function(item, id) {
                                if (isReverse)
                                    return {
                                        text: id
                                        , id: item
                                    }
                                else
                                    return {
                                        text: item
                                        , id: id
                                    }
                            })
                        };
                    }
                }
            });
        })
    }

    function getDataSelect2(id, url, placeholder = '- Pilih -', dataParams = {}, isReverse = false, checkedVal = '', isDisabled = false) {
        $(function() {
            axios.get(url, dataParams)
                .then(function(response) {
                    $('#' + id).empty();
                    $('#' + id).append($('<option>', {
                        value: ''
                    }).text(placeholder));
                    $.each(response.data, function(name, code) {
                        $('#' + id).append($('<option>', {
                            value: isReverse ? name : code
                            , text: isReverse ? code : name
                        }))
                    })
                    if (checkedVal != '')
                        $('#' + id).val(checkedVal).change()
                }).then(function() {
                    $('#' + id).select2({
                        theme: 'bootstrap4'
                    });
                    $('#' + id).attr('disabled', isDisabled)
                }).catch(error => {
                    triggerSweetalert("Gagal!", "Terjadi kegagalan, silahkan coba beberapa saat lagi! " + error, "error");
                    return false;
                })
        })
    }

    function createSummerNote(summernoteElement, height = 150) {
        summernoteElement.summernote({
            height: height
            , callbacks: callbackSummerNote()
        });
    }

    function callbackSummerNote(summernoteValidator) {
        return {
            onChange: function(contents, $editable) {
                //$(this).val(contents);
                //summernoteValidator.element($(this));
                $(this).val($(this).summernote('isEmpty') ? "" : contents);
                if (typeof summernoteValidator !== 'undefined')
                    summernoteValidator.element($(this));
            }
            , onPaste: function(e) {
                let bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');

                e.preventDefault();

                // Firefox fix
                setTimeout(function() {
                    document.execCommand('insertText', false, bufferText);
                }, 10);
            }
            , onImageUpload: function(files) {
                sendFileSummernote(files[0], $(this));
            }
            , onMediaDelete: function(targets) {
                deleteFileSummernote(targets[0].src);
            }
        }
    }

    function sendFileSummernote(file, element) {
        data = new FormData();
        data.append("file", file);
        $.ajax({
            data: data
            , type: "POST"
            , url: "/summernote/image/upload" + '?_token=' + '{{ csrf_token() }}'
            , cache: false
            , contentType: false
            , processData: false
            , success: function(response) {
                if (response.status) {
                    element.summernote("insertImage", '/' + response.path);
                    triggerToastr("Berhasil!", response.msg, "success");
                } else {
                    triggerSweetalert("Gagal!", "Terjadi kegagalan saat upload gambar, Error: " + response.msg, "error");
                }
            }
            , error: function(xhr, status, error) {
                triggerSweetalert("Gagal!", "Terjadi kegagalan saat upload gambar, silahkan coba beberapa saat lagi! Error: " + error, "error");
                return false;
            }
        });
    }

    function deleteFileSummernote(src) {
        $.ajax({
            data: {
                src: src
            }
            , type: "DELETE"
            , url: "/summernote/image/delete/" + '?_token=' + '{{ csrf_token() }}'
            , cache: false
            , success: function(response) {
                if (response.status)
                    triggerToastr("Berhasil!", response.msg, "success");
            }
        });
    }

</script>
<script>
    jQuery.extend(jQuery.validator.messages, {
        required: "Bidang isian ini wajib diisi."
        , email: "Isian ini harus berupa alamat surel yang valid."
        , maxlength: jQuery.validator.format("Harap masukkan tidak lebih dari {0} karakter")
        , minlength: jQuery.validator.format("Harap masukkan setidaknya {0} karakter")
        , max: jQuery.validator.format("Harap masukkan nilai yang kurang dari atau sama dengan {0}")
        , min: jQuery.validator.format("Harap masukkan nilai yang lebih besar dari atau sama dengan {0}")
        , number: "Isian ini harus berupa angka"
        , digits: "Isian ini harus berupa angka"
    });
    jQuery.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param * 1000000)
    }, 'File size must be less than {0} MB');
    jQuery.validator.addMethod("greaterStart", function(value, element, params) {
        return this.optional(element) || new Date(value) >= new Date($(params).val());
    }, 'Must be greater than start date.');

    $('.btn-copy').on('click', function() {
        copyDivToClipboard(this)
    })

    function copyDivToClipboard(element) {
        if ($(element).is("input")) {
            let range = document.createRange();
            range.selectNode(element);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand("copy");
            window.getSelection().removeAllRanges();
        } else {
            let input = document.createElement('textarea');
            input.innerHTML = $(element).data('text-copy');
            document.body.appendChild(input);
            input.select();
            let result = document.execCommand('copy');
            document.body.removeChild(input);
        }
        if (element.childNodes.length > 0) {
            element.getElementsByTagName('i')[0].setAttribute('class', 'fa fa-clipboard-check ml-2');
            setTimeout(function() {
                element.getElementsByTagName('i')[0].setAttribute('class', 'far fa-clipboard ml-2');
            }, 2000);
        }
        triggerToastr('Berhasil!', 'Text berhasil dicopy ke clipboard', 'success');
    }

    async function dropdownChain(id, route, sub_id, chain_sub_id, placeholder = '- Pilih -', is_id = true, isSelect2 = true, isChange = true, checkedVal = '', isReverse = false, method = 'post', params = '') {
        async function change() {
            if ($('#' + id).val()) {
                $(chain_sub_id).empty();
                $(chain_sub_id).append($('<option>', {
                    value: ''
                }).text(placeholder));
                try {
                    if (method == 'post') {
                        response = await axios.post(route, {
                            parent_id: $('#' + id).val()
                            , is_for_dropdown: 1
                        });
                    } else {
                        response = await axios.get(route + '?parent_id=' + $('#' + id).val() + '&is_for_dropdown=1&' + params);
                    }
                    $('#' + sub_id).empty();
                    $('#' + sub_id).append($('<option>', {
                        value: ''
                    }).text(placeholder));
                    $.each(response.data, function(id, name) {
                        if (isReverse)
                            [id, name] = [name, id];
                        $('#' + sub_id).append(new Option(is_id ? id + '-' + name : name, id));
                    });

                    if (checkedVal !== '') {
                        $('#' + sub_id + ' option').each(function() {
                            if (checkedVal == this.value) {
                                $(this).prop('selected', true);
                            } else {
                                $(this).prop('selected', false);
                            }
                        });
                    }

                    if (isSelect2)
                        $('#' + sub_id).select2({
                            theme: $('#' + sub_id).prop('multiple') ? 'default' : 'bootstrap4'
                        });
                } catch (error) {
                    console.error(error);
                }
            } else {
                $(chain_sub_id).empty();
                $(chain_sub_id).append($('<option>', {
                    value: ''
                }).text(placeholder));
            }
        }

        if (isChange) {
            $('#' + id).on('change', async function() {
                await change();
            });
        } else {
            await change();
        }
    }

    function initDropzone(typeDocument, typeFile, type, url, urlDestroy, loadInitCallback, acceptedFiles, maxFiles = 3, maxFilesize = 0.5) {
        let dropzoneFormElementId = '#dropzone-form-' + type;
        let dropzoneElementId = '#dropzone-' + type;
        let myDropzone = new Dropzone(dropzoneFormElementId, {
            url: url
            , headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
            , acceptedFiles: acceptedFiles
            , maxFiles: maxFiles
            , maxFilesize: maxFilesize, // dalam MB
            addRemoveLinks: true
            , dictRemoveFile: "Hapus"
            , thumbnailWidth: 120
            , thumbnailHeight: 120
            , dictInvalidFileType: `Tipe file tidak valid. Hanya file ${acceptedFiles} yang diperbolehkan.`
            , dictFileTooBig: `Ukuran file terlalu besar. Maksimal ${maxFilesize}MB`
            , init: function() {
                let myDropzone = this;
                loadInitCallback(myDropzone, typeDocument, typeFile, type);

                this.on("addedfile", function(file) {
                    // Menampilkan status loading
                    file.previewElement.querySelector(".dz-progress").classList.add("dz-loading");
                });

                this.on("thumbnail", function(file) {
                    if (file.type === "application/pdf") {
                        // For PDF files, show a PDF icon as the thumbnail
                        file.previewElement.querySelector(".dz-image").innerHTML = '<i class="pdf-thumbnail fa fa-file-pdf"></i>';
                    }
                    // Menghapus status loading setelah gambar berhasil diinisialisasi
                    file.previewElement.querySelector(".dz-progress").classList.remove("dz-loading");
                });

                // Event ketika file berhasil di-upload
                this.on("success", function(file, response) {
                    if (response.status) {
                        triggerToastr('Berhasil!', response.msg, 'success');
                        $(file.previewElement).find(".dz-image").attr("data-id", response.id);
                        $(dropzoneElementId + " .error-message").text('')
                    } else {
                        myDropzone.removeFile(file);
                        triggerToastr('Gagal!', response.msg, 'error');
                        $(dropzoneElementId + " .error-message").text(response.msg)
                    }
                });

                // Event ketika terjadi kesalahan dalam proses upload
                this.on("error", function(file, errorMessage, xhr) {
                    if (typeof errorMessage == 'object')
                        errorMessage = errorMessage.message
                    $(dropzoneElementId + " .error-message").text(errorMessage);
                });

                // Event ketika file dihapus
                this.on("removedfile", function(file) {
                    if ($(file.previewElement).find(".dz-image").data("id"))
                        $.ajax({
                            type: "post"
                            , headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                            , data: {
                                id: $(file.previewElement).find(".dz-image").data("id")
                            }
                            , url: urlDestroy
                            , success: function(response) {
                                if (response.status) {
                                    triggerToastr('Berhasil!', response.msg, 'success');
                                    $(dropzoneElementId + " .error-message").text('')
                                } else {
                                    $(dropzoneElementId + " .error-message").text(response.msg)
                                }
                            }
                            , error: function(xhr, status, error) {
                                if (error = 'error') {
                                    error = xhr.responseJSON != null ? xhr.responseJSON.message : xhr.responseText
                                }
                                $(dropzoneElementId + " .error-message").text(error);
                            }
                        });
                });
            }
            , error: function(file, message, xhr) {
                // Menghapus gambar yang error
                file.previewElement.querySelector(".dz-error-mark").style.display = "none";
            }
        });

        return myDropzone;
    }

    function generateCode(prefix = 'KATARAK-', id_target = 'code', length = 5) {
        $('#' + id_target).val(prefix + Math.random().toString(36).substr(2, length));
    }

    let Toast = Swal.mixin({
        toast: true
        , position: 'top-end'
        , showConfirmButton: false
        , timer: 3000
    });

    function triggerToastr(title, text, icon) {
        Toast.fire({
            title: title
            , text: text
            , icon: icon
        });
    }

    function triggerSweetalert(title, text, icon) {
        Swal.fire({
            title: title
            , text: text
            , icon: icon
        });
    }

    function sortList(url, element = 'tr.sortrow') {
        $("#tablecontents").sortable({
            items: "tr"
            , cursor: 'move'
            , opacity: 0.6
            , update: function() {
                sendOrderToServer(url, element);
            }
        });
    }

    function sendOrderToServer(url, element = 'tr.sortrow') {
        let order = [];
        $(element).each(function(index, element) {
            order.push({
                id: $(this).attr('data-id')
                , position: index + 1
            });
        });

        $.ajax({
            type: "POST"
            , url: url
            , data: {
                order: order
                , _token: "{{ csrf_token() }}"
            }
            , success: function(response) {
                if (!response.status) {
                    triggerSweetalert("Gagal!", "Urutan gagal diupdate, error: " + response.msg, "error");
                    return false
                } else {
                    triggerSweetalert("Berhasil!", "Urutan berhasil diupdate", "success");
                }
            }
            , error: function(xhr, status, error) {
                triggerSweetalert("Gagal!", "Terjadi kegagalan, silahkan coba beberapa saat lagi! Error: " + error, "error");
                return false;
            }
        });
    }

    let textflashData = $('.flash-data').data('text');
    let titleflashData = $('.flash-data').data('title');
    let iconflashData = $('.flash-data').data('icon');

    if (textflashData && titleflashData && iconflashData) {
        Swal.fire({
            title: titleflashData
            , text: textflashData
            , icon: iconflashData
        });
    }

    function submitFormSwal(form, message, href, is_force_delete = false, href_permanent = '') {
        Swal.fire({
            title: 'Apakah Anda yakin?'
            , text: message
            , icon: 'warning'
            , showCancelButton: true
            , showDenyButton: is_force_delete ? true : false
            , denyButtonText: is_force_delete ? 'Ya, hapus permanen' : 'Ya, lanjutkan'
            , confirmButtonColor: '#3085d6'
            , denyButtonColor: '#e74c3c'
            , cancelButtonColor: '#95a5a6'
            , confirmButtonText: 'Ya, lanjutkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.action = href;
                form.submit();
            } else if (result.isDenied) {
                form.action = href_permanent;
                form.submit();
            }
        });
    }

    function submitForm(class_name, form_id, is_data_href = false) {
        $('.' + class_name).on('click', function(e) {
            e.preventDefault();
            let form = form_id != null ? document.getElementById(form_id) : document.getElementById($(this).data('id-form'));
            let href = is_data_href ? $(this).data('href') : $(this).attr('href');
            form.action = href;
            form.submit();
        });
    }

    function confirmAction(href = "", messageConfirm = "") {
        let form = document.getElementById('confirm-form');
        submitFormSwal(form, messageConfirm, href);
    };

    function confirmDelete(href = "", text = "") {
        let form = document.getElementById('delete-form');
        submitFormSwal(form, 'Data ' + text + ' ini akan dihapus', href);
    };

    function confirmDeletePermanent(href = "", text = "", href_permanent = "") {
        let form = document.getElementById('delete-form');
        submitFormSwal(form, 'Data ' + text + ' ini akan dihapus', href, true, href_permanent);
    };

    function alertConfirm(class_name, form_id, is_message, is_data_href = false, is_permanent = false) {
        $('.' + class_name).on('click', function(e) {
            e.preventDefault();
            let messageflashData = is_message ? $(this).data('message') : 'Data ' + $(this).data('text') + ' ini akan dihapus';
            let form = form_id != null ? document.getElementById(form_id) : document.getElementById($(this).data('id-form'));
            let href = is_data_href ? $(this).data('href') : $(this).attr('href');
            let href_permanent = is_permanent ? $(this).data('hrefpermanent') : $(this).attr('href');
            let permanent = is_permanent ? true : false;
            submitFormSwal(form, messageflashData, href, permanent, href_permanent);
        });
    }

    submitForm('tombol-bulk-custom', null, true);
    submitForm('tombol-bulk-edit', null, false);

    alertConfirm('tombol-konfirmasi', 'confirm-form', true);
    alertConfirm('tombol-bulk-konfirmasi', 'bulkconfirm-form', true);
    alertConfirm('tombol-bulk-konfirmasi-custom', null, true);
    alertConfirm('tombol-hapus', 'delete-form', false);
    alertConfirm('tombol-permanent', 'delete-form', true, false, false);
    alertConfirm('tombol-hapus-permanent', 'delete-form', false, false, true);
    alertConfirm('tombol-hapus-multiple', 'bulkdestroy-form', false, true);
    alertConfirm('tombol-hapus-multiple-permanent', 'bulkdestroy-form', false, true, true);

    selectAllChecked()

    function selectAllChecked(select_all_id = 'select_all', check_class_name = 'check') {
        $('#' + select_all_id).on('click', function() {
            if (this.checked) {
                $('.' + check_class_name).each(function() {
                    this.checked = true;
                })
            } else {
                $('.' + check_class_name).each(function() {
                    this.checked = false;
                })
            }
        });
        $('.' + check_class_name).on('click', function() {
            if ($('.' + check_class_name + ':checked').length == $('.' + check_class_name).length) {
                $('#' + select_all_id).prop('checked', true);
            } else {
                $('#' + select_all_id).prop('checked', false);
            }
        })
    }

    $('.editing').attr("disabled", true);
    $('.tombol-on-edit').on('click', function() {
        let attribute = $(this).data('name');
        let id = document.getElementById(attribute);
        $(id).attr("disabled", true);
        $(this).toggleClass('change');
        $(this).css('background', '#f6c23e');
        if ($(this).hasClass("change")) {
            $(id).attr("disabled", false);
            $(this).css('background', 'green');
        }
    });

    function btnToggleCharacter() {
        $('.btn-toggle-character').on('click', function() {
            let buttonText = $(this).find('.text');
            let buttonIcon = $(this).find('i');
            let titleHidden = $(this).data('title-hidden');
            let hiddenText = $(this).data('hidden');
            let route = $(this).data('route');
            $(this).toggleClass('change');
            if ($(this).hasClass("change")) {
                if (route) {
                    axios.post(route, {
                            id: $(this).data('id')
                        })
                        .then(function(response) {
                            if (response.data.status) {
                                hiddenText = response.data.showHiddenText
                                buttonText.text(hiddenText ? hiddenText : '-')
                            }
                        }).catch(error => {
                            triggerToastr("Gagal!", "Terjadi kegagalan saat show password, silahkan coba beberapa saat lagi!", "error");
                            return false;
                        });
                }
                buttonText.text(hiddenText ? hiddenText : '-')
                buttonIcon.removeClass('fa-eye').addClass('fa-eye-slash')

                setTimeout(function() {
                    buttonText.text('Show ' + titleHidden);
                    buttonIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                    $(this).removeClass('change');
                }, $(this).data('timeout'));
            } else {
                buttonText.text('Show ' + titleHidden)
                buttonIcon.removeClass('fa-eye-slash').addClass('fa-eye')
            }
        })
    }

    function defaultValidateForm(id, rules = {}, messages = {}) {
        $('#' + id).validate({
            rules: rules
            , messages: messages
            , errorElement: 'span'
            , errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            }
            , highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            }
            , unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
            , submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: form.action
                    , type: "POST"
                    , data: $('#' + id).serialize()
                    , success: function(response) {
                        $('#msg_div').removeClass('d-none');
                        $('#res_message').show();
                        $('#res_message').html(response.msg);
                        if (!response.status) {
                            triggerToastr('Gagal!', response.msg, 'error');
                            $('#msg_div').removeClass('alert-success');
                            $('#msg_div').addClass('alert-danger');
                            $('html, body').animate({
                                scrollTop: $("#msg_div").offset().top
                            }, 500);
                        } else {
                            triggerToastr('Berhasil!', response.msg, 'success');
                            $('#msg_div').removeClass('alert-danger');
                            $('#msg_div').addClass('alert-success');
                        }
                    }
                    , error: function(xhr, status, error) {
                        triggerSweetalert("Gagal!", "Terjadi kegagalan, silahkan coba beberapa saat lagi! Error: " + error, "error");
                        return false;
                    }
                });
            }
        });
    }

    function resetUploadPreview(target_preview = null) {
        target_preview = target_preview == null ? 'img-preview' : target_preview;
        const src = $('#' + target_preview).data('src')
        $('#' + target_preview).attr('src', src);
    }

    function clearUpload(id = null, target_preview = null) {
        id = id == null ? 'thumbnail' : id;
        target_preview = target_preview == null ? 'img-preview' : target_preview;
        $('#' + id).val('');
        resetUploadPreview(target_preview);
    }

    function previewImg(id = null, target_preview = null) {
        id = id == null ? 'thumbnail' : id;
        target_preview = target_preview == null ? 'img-preview' : target_preview;
        const gambar = document.querySelector('#' + id);
        const preview_gambar = document.querySelector('#' + target_preview);

        const file_gambar = new FileReader();
        file_gambar.readAsDataURL(gambar.files[0]);

        file_gambar.onload = function(e) {
            preview_gambar.src = e.target.result;
        }
    }

</script>
