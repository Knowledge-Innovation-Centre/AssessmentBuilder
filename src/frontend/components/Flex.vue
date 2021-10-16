<template>
  <div :class="classes">
    <generic
      v-for="item in getItems(items)"
      :key="item.key"
      :class="elementClass"
      :form="item"
    />
  </div>
</template>

<script>
import itemsHelper from "../mixins/itemsHelpers";

export default {
  name: "Flex",
  components: {
    Generic: () => import("./Generic.vue")
  },
  mixins: [itemsHelper],
  props: {
    items: null,
    direction: String,
    elementClass: String
  },
  computed: {
    classes() {
      let classes = [];
      if (this.direction === "row") {
        classes.push("aoat-flex-row");
      } else {
        classes.push("aoat-flex-col");
      }
      classes.push("aoat-flex");
      return classes;
    }
  },
  methods: {}
};
</script>

<style></style>
