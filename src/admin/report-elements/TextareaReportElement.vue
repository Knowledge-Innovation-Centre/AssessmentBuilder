<template>
    <div>
      {{ object.label }}
    </div>
</template>

<script>

  export default {

    name: 'TextareaReportElement',

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
