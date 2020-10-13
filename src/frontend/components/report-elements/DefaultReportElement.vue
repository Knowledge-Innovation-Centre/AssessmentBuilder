<template>
  <div>
    <div class="aoat-font-bold">{{ object.label }}</div>
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
