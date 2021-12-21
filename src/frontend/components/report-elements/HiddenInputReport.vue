<template>
  <span />
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "HiddenInputReport",

  mixins: [labelMixin, itemsHelper],
  props: {
    object: {
      type: Object,
      required: true
    }
  },

  computed: {}
};
</script>
