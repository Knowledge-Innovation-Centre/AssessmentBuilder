<template>
  <flex :items="object.items" direction="column" />
</template>

<script>
export default {
  name: "Row",
  components: {
    Flex: () => import("./Flex.vue")
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

<style scoped></style>
