<template>
  <div>
    <flex :depth="depth" :items="items" :object="object" direction="row" />
  </div>
</template>

<script>
import Flex from "./Flex.vue";

export default {
  name: "Column",
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
  data() {
    return {};
  },
  computed: {
    items() {
      if (this.object) {
        return this.object.items;
      }
      return null;
    }
  },
  methods: {}
};
</script>

<style scoped></style>
