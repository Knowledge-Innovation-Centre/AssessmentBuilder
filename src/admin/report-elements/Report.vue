<template>
  <flex
    :depth="depth"
    :items="items"
    :object="object"
    class="report"
    direction="column"
  />
</template>

<script>
import Flex from "../components/Flex.vue";

export default {
  name: "Report",
  components: {
    Flex
  },
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
  computed: {
    items() {
      if (this.object) {
        return this.object.items;
      }
      return null;
    }
  }
};
</script>

<style scoped>
.report {
  min-height: 100px;
}
</style>
