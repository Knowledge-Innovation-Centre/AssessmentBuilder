<template>
    <div>
      {{ object.label }}
    </div>
</template>

<script>

  export default {

    name: 'FileUploadReportElement',

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
