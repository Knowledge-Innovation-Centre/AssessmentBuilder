<template>
  <div>
    <flex
      :depth="depth"
      :items="object.items"
      :object="object"
      class="row"
      direction="column"
    />
  </div>
</template>

<script>
import Flex from "./Flex.vue";

export default {
  name: "Row",
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
