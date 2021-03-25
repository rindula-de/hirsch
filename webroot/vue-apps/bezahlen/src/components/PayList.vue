<template>
    <div class="list content">
        <h3>Paypalierer</h3>
        <div class="range-slider">
            <h4>Trinkgeld:</h4>
            <input class="range-slider__range" max="5" min="0" name="tip" step="0.1" type="range" v-model="tipValue"/>
            <span class="range-slider__value">{{tipValueFixed}}€</span>
        </div>
        <hr>
        <div v-if="activeId == null" class="paypalmeslistitem self">
            <button value="self" name="id" type="submit">Selber zahlen</button>
        </div>
        <div
            :class="'paypalmeslistitem' + ((typeof activeId === 'number' && activeId === paypalme.id) ? ' active' : '')"
            v-bind:key="paypalme.id"
            v-for="paypalme in paypalmes">
            <button :value="paypalme.id" name="id" type="submit">{{paypalme.name}}</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'PayList',
        data: () => ({
            paypalmes: window.paypalmes,
            activeId: window.activeId,
            tipValue: 0.5,
        }),
        computed: {
            tipValueFixed: function () {
                return Number.parseFloat(this.tipValue).toFixed(2).replace(".", ",");
            }
        }
    }
</script>

<style scoped>

    .range-slider {
        width: 100%;
        margin-bottom: 40px;
    }

    .range-slider__range {
        -webkit-appearance: none;
        width: calc(100% - (90px));
        height: 10px;
        border-radius: 5px;
        background: #d7dcdf;
        outline: none;
        padding: 0;
        margin: 0;
    }

    .range-slider__range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #2c3e50;
        cursor: pointer;
        -webkit-transition: background 0.15s ease-in-out;
        transition: background 0.15s ease-in-out;
    }

    .range-slider__range::-webkit-slider-thumb:hover {
        background: #1abc9c;
    }

    .range-slider__range:active::-webkit-slider-thumb {
        background: #1abc9c;
    }

    .range-slider__range::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border: 0;
        border-radius: 50%;
        background: #2c3e50;
        cursor: pointer;
        -moz-transition: background 0.15s ease-in-out;
        transition: background 0.15s ease-in-out;
    }

    .range-slider__range::-moz-range-thumb:hover {
        background: #1abc9c;
    }

    .range-slider__range:active::-moz-range-thumb {
        background: #1abc9c;
    }

    .range-slider__range:focus::-webkit-slider-thumb {
        box-shadow: 0 0 0 3px #fff, 0 0 0 6px #1abc9c;
    }

    .range-slider__value {
        display: inline-block;
        position: relative;
        width: 60px;
        color: #fff;
        line-height: 20px;
        text-align: center;
        border-radius: 3px;
        background: #2c3e50;
        padding: 5px 10px;
        margin-left: 8px;
    }

    .range-slider__value:after {
        position: absolute;
        top: 8px;
        left: -7px;
        width: 0;
        height: 0;
        border-top: 7px solid transparent;
        border-right: 7px solid #2c3e50;
        border-bottom: 7px solid transparent;
        content: "";
    }

    ::-moz-range-track {
        background: #d7dcdf;
        border: 0;
    }

    input::-moz-focus-inner,
    input::-moz-focus-outer {
        border: 0;
    }

</style>
