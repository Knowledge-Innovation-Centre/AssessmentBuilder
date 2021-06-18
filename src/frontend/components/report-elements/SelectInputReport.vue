<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ value }}
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";

export default {
  name: "SelectInputReport",

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
      let key = this.$store.state.assessment[this.object.reportItemKey];

      if (!key) {
        return "/";
      }

      let foundOption = this.object.options.find(option => option.id === key);

      if (!foundOption) {
        return "/";
      }

      return foundOption.name;
    }
  },

  methods: {}
};
</script>
