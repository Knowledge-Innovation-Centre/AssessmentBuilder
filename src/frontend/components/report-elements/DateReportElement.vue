<template>
  <div>
          <h5>{{ object.label }}</h5>
          {{ date }}

    </div>
</template>

<script>

  export default {

    name: 'DateReportElement',

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
        date: ""
      };
    },

    mounted() {
      this.setDate()
    },

    methods: {
      setDate() {
        if (this.value) {
          let date = new Date(this.value);
          this.date = [date.getDay(), date.getMonth(), date.getFullYear()].join('.')
        }
      }
    }
  };
</script>
