<template>
  <div>
    {{ getLabel }}
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";

export default {
  name: "CompareScoreReport",
  components: {},
  mixins: [labelMixin],

  props: {
    object: {
      type: Object,
      required: true
    },
    depth: {
      type: Number,
      required: true
    }
  },

  data() {
    return {};
  },
  methods: {}
};
</script>
<style scoped></style>
