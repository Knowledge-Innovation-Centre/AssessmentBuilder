<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ date }}
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";

export default {
  name: "DateInputReport",

  components: {},

  mixins: [labelMixin],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      date: ""
    };
  },

  computed: {
    value() {
      return this.$store.state.assessment[this.object.reportItemKey];
    }
  },

  mounted() {
    this.setDate();
  },

  methods: {
    setDate() {
      if (this.value) {
        let date = new Date(this.value);
        this.date = [date.getDay(), date.getMonth(), date.getFullYear()].join(
          "."
        );
      }
    }
  }
};
</script>
