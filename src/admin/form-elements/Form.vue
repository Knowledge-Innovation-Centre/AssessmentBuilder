<template>
  <flex
    :depth="depth"
    :items="items"
    :object="object"
    class="form"
    direction="column"
  />
</template>

<script>
import Flex from "../components/Flex.vue";

export default {
  name: "Form",
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
.form {
  min-height: 100px;
}
</style>
