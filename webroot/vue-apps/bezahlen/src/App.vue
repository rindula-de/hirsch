<template>
    <div class="content" id="app">
        <div class="info">
            Du nutzt Paypal? Jetzt einfach bezahlen!
            Wähle einfach denjenigen aus, der heute für das Essen zuständig ist!
        </div>
        <form accept-charset="utf-8" action="/jetzt-zahlen" method="post">
            <input :value="csrfToken" autocomplete="off" name="_csrfToken"
                   type="hidden">
            <PayList/>
        </form>
        <p>Du willst auch in der Liste stehen? <span v-html="payAddLink">Hier</span> kannst du
            dich eintragen</p>
    </div>
</template>

<script>
    import PayList from './components/PayList.vue'

    export default {
        name: 'App',
        components: {
            PayList
        },
        data: () => ({
            payAddLink: window.payAddLink,
            csrfToken: window.csrfToken
        }),
    }
</script>
