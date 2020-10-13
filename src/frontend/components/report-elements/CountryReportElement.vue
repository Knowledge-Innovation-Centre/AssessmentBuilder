<template>
  <div>
    <div class="aoat-font-bold">{{ object.label }}</div>
    {{ value }}
  </div>
</template>

<script>

  export default {

    name: 'CountryReportElement',

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
        let key =  this.$store.state.assessment[this.object.reportItemKey]
        if (!key) {
          return '/';
        }
        return this.object.options.find(option => option.id === key).name
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
