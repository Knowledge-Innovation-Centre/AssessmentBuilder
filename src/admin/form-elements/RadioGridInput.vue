<template>
  <div class="aoat-flex aoat-flex-col">
    <strong class="aoat-m-1">Label/Question:</strong>
    <input
      v-model="object.label"
      class="aoat-w-full aoat-m-1"
      placeholder="Enter question here"
      type="text"
    />
    <strong class="aoat-m-1">Options vertical:</strong>

    <table class="aoat-w-full">
      <thead>
        <tr>
          <th>Unique ID</th>
          <th>Value</th>
          <th>Score</th>
          <th>Color</th>
          <th>Icon</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(option, index) in object.optionsVertical" :key="option.id">
          <td><input v-model="option.id" type="text" /></td>
          <td><input v-model="option.name" type="text" /></td>
          <td><input v-model="option.score" type="number" /></td>
          <td>
            <v-swatches v-model="option.color" :swatches="swatches" />
          </td>
          <td>
            <multiselect
              v-model="option.icon"
              placeholder=""
              :show-labels="false"
              :options="dashicons"
            >
              <template slot="singleLabel" slot-scope="props">
                <span :class="props.option" class="dashicons" />
              </template>
              <template slot="option" slot-scope="props">
                <span :class="props.option" class="dashicons" />
              </template>
            </multiselect>
          </td>
          <td>
            <button class="aoat-h-6" @click="removeVerticalOption(index)">
              X
            </button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6" style="text-align: center">
            <button @click="addOptionVertical()">+</button>
          </td>
        </tr>
      </tfoot>
    </table>

    <strong class="aoat-m-1">Options horizontal:</strong>
    <table>
      <thead>
        <tr>
          <th>Unique ID</th>
          <th>Value</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(option, index) in object.optionsHorizontal"
          :key="option.id"
        >
          <td class="aoat-w-32"><input v-model="option.id" type="text" /></td>
          <td>
            <input v-model="option.name" type="text" class="aoat-w-full" />
          </td>
          <td class="aoat-w-12">
            <button class="aoat-h-6" @click="removeHorizontalOption(index)">
              X
            </button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" style="text-align: center">
            <button @click="addOptionHorizontal()">+</button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
import VSwatches from "vue-swatches";
import { Multiselect } from "vue-multiselect";
import dashicons from "../utils/dashicons";

// Import the styles too, globally
import "vue-swatches/dist/vue-swatches.css";
export default {
  name: "RadioGridInput",

  components: {
    VSwatches,
    Multiselect
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
      dashicons: dashicons,
      swatches: this.$store.state.settings.available_colors
    };
  },
  methods: {
    addOptionHorizontal() {
      this.object.optionsHorizontal.push({
        id: "",
        name: ""
      });
    },
    addOptionVertical() {
      this.object.optionsVertical.push({
        id: "",
        name: "",
        score: 1,
        color: "",
        icon: null
      });
    },

    removeHorizontalOption(index) {
      this.object.optionsHorizontal.splice(index, 1);
    },

    removeVerticalOption(index) {
      this.object.optionsVertical.splice(index, 1);
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
/deep/ .multiselect__input {
  opacity: 0;
}
</style>
