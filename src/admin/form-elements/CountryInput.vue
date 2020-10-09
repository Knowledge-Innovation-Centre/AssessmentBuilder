<template>
  <div class="aoat-flex aoat-flex-col">
    <strong class="aoat-m-1">Label/Question:</strong>
    <input class="aoat-w-full aoat-m-1" placeholder="Enter question here" v-model="object.label" type="text" >
    </div>
</template>

<script>


  export default {

    name: 'CountryInput',


    components: {

    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data () {
      return {
        show: false,
      };
    },
    methods: {
    }
  };
</script>
<style scoped>
</style>
