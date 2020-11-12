<template>
  <div>

    <div class="more-options-button">
      <tippy arrow
             :interactive="true"
             theme="light"
             :max-width="800"
             placement="left"
             class="aoat-inline-block"
             @show="checkKeys()"
             trigger="click">
        <template v-slot:trigger>
          <button class="aoat-px-0 aoat-cursor-pointer">
            <span class="dashicons dashicons-admin-generic"></span>
          </button>
        </template>
        <div class="aoat-text-left w-800 aoat-py-4">
          <table class="table aoat-w-full w-800">
            <tbody>
            <tr>
              <th>Label:</th>
              <td><input  v-model="object.reportLabel" type="text"></td>
            </tr>
            <tr v-if="typeof object.hidden !== 'undefined'">
              <th>Hidden:</th>
              <td><input  v-model="object.hidden" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.hideLabels !== 'undefined'">
              <th>Hide labels:</th>
              <td><input  v-model="object.hideLabels" type="checkbox"></td>
            </tr>
            <tr>
              <th>Classes:</th>
              <td><input v-model="object.class" type="text"></td>
            </tr>
            <tr v-if="object.type === 'radio_grid'">
              <th>Select graph</th>
              <td>
                  <multiselect
                      v-model="object.selectedGraph"
                      :multiple="true"
                      label="label"
                      track-by="key"
                      :allow-empty="false"
                      class="aoat-w-full"
                      :options="availableGraphs">
                  </multiselect>
              </td>
            </tr>
            <tr v-if="typeof object.selectedResultType !== 'undefined'">
              <th>Select type</th>
              <td>
                  <multiselect
                      v-model="object.selectedResultType"
                      :multiple="true"
                      label="label"
                      track-by="key"
                      :allow-empty="false"
                      class="aoat-w-full"
                      :options="availableResultTypes">
                  </multiselect>
              </td>
            </tr>
            <tr v-if="typeof object.legendFor !== 'undefined'">
              <th>Select score</th>
              <td>
                  <multiselect
                      v-model="object.legendFor"
                      :multiple="false"
                      label="reportLabel"
                      track-by="reportItemKey"
                      :allow-empty="false"
                      class="aoat-w-full"
                      :options="availableScores">
                  </multiselect>
              </td>
            </tr>
            <tr v-if="typeof object.scoreGraphColor !== 'undefined'">
              <th>Select score graph color</th>
              <td>
                <v-swatches :swatches="swatches"
                            v-model="object.scoreGraphColor"></v-swatches>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </tippy>
      <button class="aoat-px-0 aoat-cursor-pointer" @click="toggleShow()">
        <span v-if="object.hideInForm" class="dashicons aoat-text-orange-600 dashicons-visibility"></span>
        <span v-else class="dashicons dashicons-hidden"></span>
      </button>
      <tippy arrow
             ref="remove_element"
             :interactive="true"
             theme="light"
             :max-width="800"
             placement="left"
             class="aoat-inline-block"
             @show="checkKeys()"
             trigger="click">
        <template v-slot:trigger>
          <button class="aoat-px-0 aoat-cursor-pointer">
            <span class="dashicons dashicons-trash"></span>
          </button>
        </template>
        <div>Are you sure?</div>
        <button class="aoat-mt-2" @click="remove()">Confirm</button>
      </tippy>
    </div>
    <span class="handle dashicons dashicons-move" :class="getHandleClass()"></span>
  </div>
</template>

<script>

  import {Multiselect} from "vue-multiselect";
  import formElements from '../utils/form-elements.js'
import VSwatches from 'vue-swatches'
  import "vue-swatches/dist/vue-swatches.css"

  export default {

    name: 'CommonOptionsReport',

    components: {
      Multiselect,
      VSwatches
    },

    props: {
      object: {
        type: Object,
        required: true,
      },
      depth: {
        type: Number,
        required: true
      }
    },

    computed: {
      availableScores() {
        return this.getItemsRecursive(this.$store.state.report.items)
            .filter(field => ['part_score', 'total_score'].includes(field.type))
      }
    },

    data () {
      return {
        swatches: this.$store.state.settings.available_colors,
        availableGraphs: [
          {
            label: "Pie",
            key: "pie",
          },
          {
            label: 'Radar',
            key: 'radar'
          },
          {
            label: 'Grid',
            key: 'grid'
          }
        ],
        availableResultTypes: [
          {
            label: 'Score',
            key: 'score'
          },
          {
            label: "Pie",
            key: "pie",
          },
          {
            label: 'Radar',
            key: 'radar'
          },
          {
            label: 'Bar',
            key: 'bar'
          },
          {
            label: 'Horizontal bar',
            key: 'horizontal_bar'
          },
        ]
      };
    },

    methods: {

      getItemsRecursive(items) {
        for (let item of items) {
          if (item.items) {
            items = items.concat(this.getItemsRecursive(item.items))
          }
        }
        return items;
      },
      remove() {
        this.$store.dispatch('removeFieldReport', this.object.key);
      },
      getHandleClass() {
        if (this.object.type === 'page') {
          return 'handle-page' + this.depth
        }
        if (this.object.type === 'column') {
          return 'handle-column' + this.depth
        }
        if (this.object.type === 'column') {
          return 'handle-row' + this.depth
        }
        return 'handle-other' + this.depth
      },
      toggleShow() {
        this.$set(this.object, 'hideInForm', !this.object.hideInForm)
      },
      checkKeys() {
        let element = formElements.find(el => el.type === this.object.type)

        Object.keys(element).forEach(key => {
          if (typeof this.object[key] === 'undefined') {
            this.$set(this.object, key, element[key]);
          }
        })
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
  /deep/ .multiselect__input {
    display: none;
  }
  .handle {
    position: absolute;
    left: -10px;
    top: -20px;
    cursor: grab;
  }
  td {
    max-width: 250px;
    min-width: 200px;
  }
  select {
    width: 100%;
  }
</style>
