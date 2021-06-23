<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ getReportValue(object) }}
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "TextareaInputReport",

  mixins: [labelMixin, itemsHelper],

  props: {
    object: {
      type: Object,
      required: true
    }
  }
};
</script>
