<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ value }}
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";

export default {
  name: "RadioInputReport",

  components: {},

  mixins: [labelMixin],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {};
  },

  computed: {
    value() {
      return this.$store.state.assessment[this.object.reportItemKey];
    }
  },

  methods: {}
};
</script>
