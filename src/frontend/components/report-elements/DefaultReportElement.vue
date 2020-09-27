<template>
  <div>
          <h5>{{ object.label }}</h5>
          {{ value }}

    </div>
</template>

<script>

  export default {

    name: 'DefaultReportElement',

    components: {

    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
      value() {
          return this.$store.state.assessment[this.object.reportItemKey]
      }
    },

    data () {
      return {
      };
    },

    methods: {
    }
  };
</script>
