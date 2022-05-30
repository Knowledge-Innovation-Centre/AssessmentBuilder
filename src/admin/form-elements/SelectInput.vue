<template>
  <div class="aoat-flex aoat-flex-col">
    <strong class="aoat-m-1">Label/Question:</strong>
    <input
      v-model="object.label"
      class="aoat-w-full aoat-m-1"
      placeholder="Enter question here"
      type="text"
    />
    <strong class="aoat-m-1">Options:</strong>
    <table class="aoat-w-full">
      <thead>
        <tr>
          <th>Unique ID</th>
          <th>Value</th>
          <th>Score</th>
          <th>Color</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(option, index) in object.options">
          <td class="aoat-w-32">
            <input v-model="option.id" type="text" class="aoat-w-full" />
          </td>
          <td>
            <input v-model="option.name" type="text" class="aoat-w-full" />
          </td>
          <td class="aoat-w-12">
            <input v-model="option.score" type="number" class="aoat-w-full" />
          </td>
          <td class="aoat-w-12">
            <v-swatches v-model="option.color" :swatches="swatches" />
          </td>
          <td class="aoat-w-12">
            <button class="aoat-h-6" @click="removeOption(index)">X</button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" style="text-align: center">
            <button @click="addOption()">+</button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
import VSwatches from "vue-swatches";

// Import the styles too, globally
import "vue-swatches/dist/vue-swatches.css";

export default {
  name: "SelectInput",

  components: {
    VSwatches
  },

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      show: false,
      swatches: this.$store.state.settings.available_colors
    };
  },
  methods: {
    addOption() {
      this.object.options.push({
        id: "",
        name: "",
        score: 1,
        color: "#E84B3C"
      });
    },
    removeOption(index) {
      this.object.options.splice(index, 1);
    }
  }
};
</script>
<style scoped>
.table > tbody > tr > th,
.table > tbody > tr > td {
  padding: 5px 10px;
}
.table > tbody > tr > th {
  text-align: right;
}
</style>
