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
          <th>Dimension</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(option, index) in object.options" :key="option.id">
          <td class="aoat-w-32">
            <input v-model="option.id" type="text" class="aoat-w-full" />
          </td>
          <td class="aoat-w-64">
            <input v-model="option.name" type="text" class="aoat-w-full" />
          </td>
          <td>
            <multiselect
              :value="option.dimensions"
              :multiple="true"
              label="post_title"
              placeholder="Select one"
              class="aoat-w-full aoat-min-w-60"
              track-by="ID"
              :options="dimensions"
              :searchable="true"
              :loading="isLoading"
              :internal-search="false"
              :clear-on-select="false"
              :close-on-select="false"
              :hide-selected="true"
              @input="selectValue($event, option)"
              @search-change="asyncFind"
            />
          </td>
          <td class="aoat-w-12">
            <button class="aoat-h-6" @click="removeOption(index)">X</button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" style="text-align: center">
            <button @click="addOption()">+</button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
import { Multiselect } from "vue-multiselect";
import Api from "../Api";

export default {
  name: "LOCSelectInput",

  components: {
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
      dimensions: [],
      isLoading: false
    };
  },
  methods: {
    addOption() {
      this.object.options.push({
        id: "",
        name: "",
        dimensions: []
      });
    },
    asyncFind(query) {
      this.isLoading = true;
      Api.get(`${aoat_config.aoatGetSubsetItemsUrl}?search=${query}`)
        .then(result => {
          this.dimensions = result.data;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    removeOption(index) {
      this.object.options.splice(index, 1);
    },
    selectValue(selectedDimensions, option) {
      option.dimensions = selectedDimensions.map(selectedDimension => {
        return {
          ID: selectedDimension.ID,
          post_title: selectedDimension.post_title
        };
      });
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
