$(document).ready(function() {
    var productArr = [];
    var siteArr = [];
    var cityArr = [];
    var operArr = [];
    var productField = $('#product-name');
    var productList = {
        index: 0
    };
    var changeOrderSummary = (function() {
        return {
            plus: function(amount) {
                if (amount) {
                    order["summary"] += amount;
                    changeOrderSummary.refresh();
                    console.log(amount + " plus-" + order["summary"]);
                }
            },
            minus: function(amount) {
                if (amount) {
                    order["summary"] -= amount;
                    changeOrderSummary.refresh();
                    console.log(amount + " minus-" + order["summary"]);
                }
            },
            refresh: function() {
                if (!parseInt(order["summary"])) {
                    $(".summary").html(0);
                } else {
                    $(".summary").html(parseInt(order["summary"]));
                }
            }
        };
    })();
    var order = {
        operatorId: "",
        siteName: "",
        siteUrl: "",
        clientName: "",
        clientCity: "",
        orderType: "",
        phoneNumber: "",
        orderDate: "",
        orderTime: "",
        orderAddress: "",
        shippingCost: 0,
        logistComment: "",
        summary: 0,
    };

    $('#form-link').sidr();
    $.sidr('open');


    new Pikaday({
        field: document.getElementById('datepicker'),
        firstDay: 1,
        trigger: document.getElementById('datepicker-button'),
        format: 'l',
        minDate: new Date('2014-01-01'),
        maxDate: new Date('2020-12-31'),
        yearRange: [2014, 2020],
        onSelect: function() {
            order["orderDate"] = this.getMoment().format('l');
        },
        i18n: {
            previousMonth: 'Предыдущий месяц',
            nextMonth: 'Следующий месяц',
            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            weekdaysShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
        }
    });


    function checkFirstStep() {
        if (order["operatorId"] !== "" && order["siteName"] !== "" && order["clientName"] !== "" && order["clientCity"] !== "")
            return true;
        return false;
    }

    function checkNewOrder() {
        if (order["phoneNumber"] !== "" && order["orderDate"] !== "" && order["orderTime"] !== "" && order["orderAddress"] !== "" && Object.keys(productList).length > 1) {
            if ($("#cityList").val() !== "other" && order["shippingCost"] === "") {
                return false;
            }
            return true;
        }
        return false;
    }

    function validateSelection() {
        if ($.inArray(productField.val(), productArr) === -1) {
            productField.val("");
            return false;
        }
        return true;
    }

        $.getJSON("storage/data.json", function(data) {
            productArr = data.products;
            siteArr = data.sites;
            cityArr = data.cities;
            operArr = data.operators;
            var products = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                local: $.map(productArr, function(product) {
                    return {
                        name: product
                    };
                }),
                limit: 10
            });
            products.initialize();
            productField.typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                displayKey: 'name',
                source: products.ttAdapter()
            }).blur(validateSelection);

            $.each(siteArr, function(key, value) {
                var newOpt = new Option(key, key);
                newOpt.setAttribute("id", value["url"]);
                $("#siteList").append(newOpt);
            });

            $.each(cityArr, function(key, value) {
                $("#cityList").append(new Option(value, key));
            });

            $.each(operArr, function(key, value) {
                $("#operatorList").append(new Option(value, key));
            });

            $("#siteList").change(function() {
                var city = "other";
                var currentVal = $(this).val();
                var currentUrl = $(this).find("option:selected").attr("id");
                if (currentVal !== "null") {
                    city = siteArr[currentVal].mainCity;
                }
                order["siteName"] = currentVal;
                order["siteUrl"] = currentUrl;
                $("#cityList").val(city);
                $("#cityList").change();
            });

            $("#cityList").change(function() {
                var currentVal = $(this).val();
                if (currentVal == "other") {
                    $("#otherCity").removeAttr("hidden");
                    order["clientCity"] = "";
                    $("#shippingCost").attr("hidden", true);
                } else {
                    $("#otherCity").attr("hidden", true);
                    $("#otherCity").val("");
                    $("#shippingCost").removeAttr("hidden");
                    order["clientCity"] = cityArr[currentVal];
                }
                $("#paymentNotice").html("");
                if (order["siteName"] !== "null")
                    $("#paymentNotice").html(siteArr[order["siteName"]]["shipping"][currentVal]);
            });
        });

    $("#operatorList").change(function() {
        var currentVal = $(this).find('option:selected').text();
        order["operatorId"] = currentVal;
        console.log(order);
    });
    $("#clientName").change(function() {
        var currentVal = $(this).val();
        order["clientName"] = currentVal;
    });
    $("#phoneNumber").change(function() {
        var currentVal = $(this).val();
        order["phoneNumber"] = currentVal;
    });
    $("#orderDate").change(function() {
        var currentVal = $(this).val();
        order["orderDate"] = currentVal;
    });
    $("#orderTime").change(function() {
        var currentVal = $(this).val();
        order["orderTime"] = currentVal;
    });
    $("#orderAddress").change(function() {
        var currentVal = $(this).val();
        order["orderAddress"] = currentVal;
    });
    $("#shippingCost").change(function() {
        var currentVal = $(this).val();
        changeOrderSummary.minus(parseInt(order["shippingCost"]));
        changeOrderSummary.plus(parseInt(currentVal));
        order["shippingCost"] = currentVal;
    });
    $("#otherCity").change(function() {
        var currentVal = $(this).val();
        order["clientCity"] = currentVal;
    });
    $("#logistComment").change(function() {
        var currentVal = $(this).val();
        order["logistComment"] = currentVal;
    });
    $(".prod-list").on('click', 'a.delProd', function(event) {
        event.preventDefault();
        var parent = $(this).parent().parent();
        changeOrderSummary.minus(parseInt(parent.find(".prod-price").html()));
        $(this).parent().parent().remove();
        delete productList[parent.attr("id")];
    });
    $(".addProd").click(function(event) {
        event.preventDefault();
        var nameEl = productField;
        var priceEl = $("#product-price");
        if (validateSelection() && nameEl.val() && priceEl.val()) {
            changeOrderSummary.plus(parseInt(priceEl.val()));
            var index = productList.index;
            var key = "prod-" + index;
            productList[key] = nameEl.val() + ": " + priceEl.val();
            productList.index = index + 1;
            var newEl = $(".prod-list .example").clone().insertAfter(".prod-list .example").attr("id", key);
            newEl.find(".prod").html(nameEl.val());
            nameEl.val('');
            newEl.find(".prod-price").html(priceEl.val()).val('');
            priceEl.val('');
            newEl.removeAttr("hidden").removeClass("example");
        }
    });
    $("#newOrder").click(function(event) {
        event.preventDefault();
        $(".forms form").attr("hidden", true);
        if (checkFirstStep()) {
            $("#siteFrame").attr("src", order["siteUrl"]);
            $("#newOrderForm").removeAttr("hidden");
            $("#productForm").removeAttr("hidden");
        }
    });
    $("#oldOrder").click(function(event) {
        event.preventDefault();
        $(".forms form").attr("hidden", true);
        if (checkFirstStep()) {}

    });
    $("#sendNewOrder").click(function(event) {
        event.preventDefault();
        if (checkNewOrder()) {
            delete productList["index"];
            order["products"] = productList;
            order["mail-from"] = siteArr[order["siteName"]]["mail-from"];
            order["mail-to"] = siteArr[order["siteName"]]["mail-to"];
            $.post("sendmail.php", order, function(data) {
                console.log(data);
                if (data == "ok")
                    location.reload();
                else
                    alert(data);
            });
        }
    });
    $("#reject").click(function(event) {
        event.preventDefault();
        location.reload();
    });
});