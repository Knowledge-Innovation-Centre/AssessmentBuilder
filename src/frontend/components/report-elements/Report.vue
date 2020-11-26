<template>
  <div>
    <flex :items="items" class="form" direction="column" />
  </div>
</template>

<script>
import Flex from "../Flex.vue";

export default {
  name: "Report",
  components: {
    Flex
  },
  props: {
    object: {
      type: Object,
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
