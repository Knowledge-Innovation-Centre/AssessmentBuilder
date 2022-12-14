<template>
  <div id="search-catalog-items" />
</template>

<script>
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "LOCItemsReport",

  mixins: [itemsHelper],
  props: {
    object: {
      type: Object,
      required: true
    }
  },
  computed: {
    filteredItems() {
      return this.getPage(this.object.key).items;
    },
    dimensions() {
      return this.getLocDimensions(this.$store.state.assessment).join(",");
    }
  },

  created() {
    let url = new URL(window.location.href);
    url.searchParams.set("dimensions", this.dimensions);

    window.history.pushState({ path: url.href }, "", url.href);
  }
};
</script>
