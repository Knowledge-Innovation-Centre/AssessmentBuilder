<template>
  <div class="aoat-flex aoat-flex-col">
    <strong class="aoat-m-1">Label/Question:</strong>
    <input class="aoat-w-full aoat-m-1" placeholder="Enter question here" v-model="object.label" type="text" >
    <strong class="aoat-m-1">Options vertical:</strong>

    <table>
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
      <tr v-for="(option, index) in object.optionsHorizontal">
        <td  class="aoat-w-32"><input type="text" v-model="option.id"></td>
        <td><input type="text" class="aoat-w-full" v-model="option.name"></td>
        <td class="aoat-w-12"><input type="number" v-model="option.score"></td>
        <td class="aoat-w-12">    <v-swatches v-model="option.color"></v-swatches></td>
        <td class="aoat-w-12"><button class="aoat-h-6" @click="removeHorizontalOption(index)">X</button></td>
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
      <tr v-for="(option, index) in object.optionsVertical">
        <td class="aoat-w-32"><input type="text" v-model="option.id"></td>
        <td><input type="text" class="aoat-w-full" v-model="option.name"></td>
        <td class="aoat-w-12"><button class="aoat-h-6" @click="removeVerticalOption(index)">X</button></td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" style="text-align: center">
          <button @click="addOptionVertical()">+</button>
        </td>
      </tr>
      </tfoot>
    </table>

    </div>
</template>

<script>
import VSwatches from 'vue-swatches'

// Import the styles too, globally
import "vue-swatches/dist/vue-swatches.css"
  export default {
    name: 'RadioGridInput',

    components: {
      VSwatches
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data () {
      return {
        show: false,
      };
    },
    methods: {
      addOptionVertical() {
        this.object.optionsVertical.push({
          id: "",
          name: ""
        })
      },
      addOptionHorizontal() {
        this.object.optionsHorizontal.push({
          id: "",
          name: "",
          score: 1,
          color: '#E84B3C',
        })
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

.table > tbody > tr > th, .table > tbody > tr > td {
  padding: 5px 10px;
}
.table > tbody > tr > th {
  text-align: right;
}
</style>
