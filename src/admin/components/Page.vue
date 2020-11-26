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
  name: "Page",
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
  methods: {}
};
</script>
<style scoped></style>
