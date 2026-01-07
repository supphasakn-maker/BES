fn.app.production_pmr.in.dialog_add = function () {
    $.ajax({
        url: "apps/production_pmr/view/dialog.in.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_in" });


            var observer = new MutationObserver(function (mutations) {
                var roundSelect = $("form[name=form_addin] select[name=round]");
                if (roundSelect.length > 0) {
                    observer.disconnect();
                    setupRoundSelect(roundSelect);
                }
            });

            observer.observe(document.getElementById('dialog_add_in'), {
                childList: true,
                subtree: true
            });


            var checkCount = 0;
            var checkInterval = setInterval(function () {
                var roundSelect = $("form[name=form_addin] select[name=round]");
                checkCount++;

                if (roundSelect.length > 0 || checkCount > 20) {
                    clearInterval(checkInterval);
                    if (roundSelect.length > 0) {
                        setupRoundSelect(roundSelect);
                    }
                }
            }, 100);

            function setupRoundSelect(roundSelect) {
                console.log('Setting up round select...');


                if (!roundSelect.hasClass('select2-hidden-accessible')) {
                    setTimeout(function () {
                        bindRoundEvents(roundSelect);
                    }, 500);
                } else {
                    bindRoundEvents(roundSelect);
                }
            }

            function bindRoundEvents(roundSelect) {
                console.log('Binding round select events...');


                roundSelect.off('.customRound');


                roundSelect.on('change.customRound', function () {
                    handleRoundChange($(this).val());
                });


                roundSelect.on('select2:select.customRound', function (e) {
                    handleRoundChange(e.params.data.id);
                });


                $(document).on('click.customRound', '.select2-results__option', function () {
                    setTimeout(function () {
                        var selectedValue = roundSelect.val();
                        if (selectedValue) {
                            handleRoundChange(selectedValue);
                        }
                    }, 100);
                });


                roundSelect.on('input.customRound', function () {
                    handleRoundChange($(this).val());
                });


                setTimeout(function () {
                    var initialValue = roundSelect.val();
                    if (initialValue && initialValue !== '') {
                        console.log('Loading initial value:', initialValue);
                        handleRoundChange(initialValue);
                    }
                }, 1000);

                console.log('Round select events bound successfully');
            }

            function handleRoundChange(selectedRound) {
                console.log('Round changed to:', selectedRound);

                if (!selectedRound || selectedRound === '') {
                    clearWeight();
                    return;
                }


                var weightInput = $("#dialog_add_in input[name='weight_out_total'], #dialog_add_in input[type='number']");
                if (weightInput.length > 0) {
                    weightInput.prop('disabled', true).val('Loading...');
                }

                $.ajax({
                    url: "apps/production_pmr/xhr/action-load-round.php",
                    type: "POST",
                    data: { round: selectedRound },
                    dataType: "json",
                    success: function (response) {
                        console.log('AJAX response:', response);

                        if (weightInput.length > 0) {
                            weightInput.prop('disabled', false);

                            if (response && response.products && response.products.amount) {
                                weightInput.val(response.products.amount);
                            } else {
                                weightInput.val('');
                            }

                            weightInput.trigger('change');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', error);
                        if (weightInput.length > 0) {
                            weightInput.prop('disabled', false).val('');
                        }
                    }
                });
            }

            function clearWeight() {
                var weightInput = $("#dialog_add_in input[name='weight_out_total'], #dialog_add_in input[type='number']");
                if (weightInput.length > 0) {
                    weightInput.val('').trigger('change');
                }
            }


            $("#dialog_add_in").on('hidden.bs.modal', function () {
                $(document).off('.customRound');
                console.log('Modal closed, events cleaned up');
            });
        }
    });
};
fn.app.production_pmr.in.add = function (id) {
    $.post("apps/production_pmr/xhr/action-add-in.php", $("form[name=form_addin]").serialize(), function (response) {
        if (response.success) {
            $("#tblIn").DataTable().draw();
            $("#dialog_add_in").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};



$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "add_circle_outline",
    onclick: "fn.app.production_pmr.in.dialog_add()",
    caption: "เพิ่มการรับเข้า"
}));
