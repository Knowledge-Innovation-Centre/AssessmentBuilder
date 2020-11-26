<template>
  <flex :items="items" direction="row" element-class="aoat-p-2" />
</template>

<script>
export default {
  name: "Column",
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
