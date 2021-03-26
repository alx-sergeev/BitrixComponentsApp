'use strict';

document.addEventListener('DOMContentLoaded', function() {
    // Необходимые элементы.
    let base = BX.message('arParams');
    if(!base || base === undefined) return;

    let bCalc = document.getElementById('fuelCalc');
    let sliderInp = bCalc.querySelector('#calculator__input');
    let eventChange = new Event('change');
    let paramsCalc = bCalc.querySelector('#calculator__params');
    let addressInp = bCalc.querySelector('#calculator__address');
    let products = bCalc.querySelectorAll('input[name="productCalc"]');
    let distances = bCalc.querySelectorAll('input[name="distanceMkad"]');
    let elemPriceLiter = bCalc.querySelector('.calculator__price-liter span');
    let elemPriceTotal = bCalc.querySelector('.calculator__price-total span');
    let orderBtn = bCalc.querySelector('#calculator__order-btn');

    let sliderline = $('#calculator__slider').slider({
        range: 'max',
        min: 100,
        max: 5000,
        value: 1,
        slide: function( event, ui ) {
            sliderInp.value = ui.value;
            sliderInp.dispatchEvent(eventChange);
        }
    });
    sliderInp.addEventListener('keyup', function() {
        sliderline.slider({ "value": $(this).val()});
    });

    // Цена внутри МКАД и за МКАД.
    let inMkadPrice = parseFloat(base['PRICE_IN_MKAD']).toFixed(2);
    let outMkadPrice = parseFloat(base['PRICE_OUT_MKAD']).toFixed(2);

    // Цены за 1 литр, при указанном объеме.
    let productMask = ['100_500', '501_1000', '1001_2500', '2501_5000'];
    let product1 = [];
    let product2 = [];
    let product3 = [];

    productMask.forEach(function(item, index) {
        product1[ item ] = parseFloat(base['PRICES_PRODUCT_ONE'][ index ]).toFixed(2);
        product2[ item ] = parseFloat(base['PRICES_PRODUCT_TWO'][ index ]).toFixed(2);
        product3[ item ] = parseFloat(base['PRICES_PRODUCT_THREE'][ index ]).toFixed(2);
    });

    let fuelCalculator = {
        product: '',
        mkad: '',
        liter: 0,
        literPrice: 0,
        total: 0,

        // Считает и выводит цену за 1 литр топлива.
        countLiterPrice: function(productType) {
            let literCnt = sliderInp.value;
            let priceCloneVar = {};

            if(productType == 'ЕВРО-3') {
                Object.assign(priceCloneVar, product1);
            } else if (productType == 'ЕВРО-4') {
                Object.assign(priceCloneVar, product2);
            } else if (productType == 'ЕВРО-5') {
                Object.assign(priceCloneVar, product3);
            }

            if(literCnt >= 100 && literCnt <= 500) {
                this.literPrice = parseFloat(priceCloneVar['100_500']);
            } else if(literCnt > 500 && literCnt <= 1000) {
                this.literPrice = parseFloat(priceCloneVar['501_1000']);
            } else if(literCnt > 1000 && literCnt <= 2500) {
                this.literPrice = parseFloat(priceCloneVar['1001_2500']);
            } else if(literCnt > 2500 && literCnt <= 5000) {
                this.literPrice = parseFloat(priceCloneVar['2501_5000']);
            }

            this.liter = Number(literCnt);
            this.literPrice = parseFloat(this.literPrice).toFixed(2);

            elemPriceLiter.innerHTML = this.literPrice + ' руб./л';
        },

        // Считает и выводит общую стоимость.
        countTotalPrice: function() {
            let _self = this;
            let liter = this.liter;
            let literSum = this.literPrice;
            let mkadPrice = 0;

            distances.forEach(function(elem){
                if(elem.checked) {
                    if(elem.value == 'внутри МКАД') {
                        mkadPrice = inMkadPrice;
                    } else if (elem.value == 'за МКАД') {
                        mkadPrice = outMkadPrice;
                    }

                    _self.mkad = elem.value;
                }
            });

            if(liter && literSum && mkadPrice) {
                this.total = (liter * literSum) + parseFloat(mkadPrice);
            }

            this.total = this.total.toFixed(2);

            elemPriceTotal.innerHTML = this.total + ' руб.';
        },
        countPriceAction: function() {
            let _self = this;
            let checkProduct = false;

            products.forEach(function(elem){
                if(elem.checked) {
                    _self.product = elem.value;

                    checkProduct = true;
                }
            });

            if(checkProduct) {
                this.countLiterPrice(this.product);
                this.countTotalPrice();
            } else {
                elemPriceTotal.innerHTML = '0.00 руб.';
                elemPriceLiter.innerHTML = '0.00 руб./л';
            }
        },

        // Заполняем тег параметрами из калькулятора
        // для дальнейшего заполнения формы.
        orderCalculatorAction: function(e) {
            if(!addressInp.value) {
                addressInp.parentElement.classList.add('error');

                orderBtn.dataset.event = 'jqm-lock';

                e.preventDefault();
            } else {
                addressInp.parentElement.classList.remove('error');

                orderBtn.dataset.event = 'jqm';
            }

            if(!this.product || !this.mkad || !this.liter || !this.literPrice || !this.total) {
                e.preventDefault();
            }

            paramsCalc.setAttribute('data-address', addressInp.value);
            paramsCalc.setAttribute('data-product', this.product);
            paramsCalc.setAttribute('data-mkad', this.mkad);
            paramsCalc.setAttribute('data-fuel-volume', this.liter);
            paramsCalc.setAttribute('data-price-liter', this.literPrice);
            paramsCalc.setAttribute('data-total', this.total);
        },
    };

    // Калькуляция. Обработчик события для калькуляции.
    bCalc.querySelectorAll('#fuelCalc input').forEach(function(elem){
        elem.addEventListener('change', function() {
            fuelCalculator.countPriceAction();
        });
    });

    // Событие при нажатии на кнопку "Оставить заявку".
    orderBtn.addEventListener('click', function(event) {
        fuelCalculator.orderCalculatorAction(event);
    });
});