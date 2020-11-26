<template>
  <div>
    <h2 v-if="object.showTitle">{{ object.name }}</h2>
    <flex :items="object.items" :object="object" direction="column" />
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
    }
  },

  data() {
    return {};
  },
  methods: {}
};
</script>
<style scoped></style>
