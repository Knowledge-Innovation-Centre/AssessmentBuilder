<template>
  <div id="search-catalog-items" />
</template>

<script>
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "LOCItemsReport",

  mixins: [itemsHelper],

  computed: {
    filteredItems() {
      return this.getItems(
        this.$store.state.assessmentObject.form.form_data.items
      ).filter(item => item.type === "page");
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
