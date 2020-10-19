<template>
  <div>

    <div class="more-options-button">
      <tippy arrow
             :interactive="true"
             theme="light"
             :max-width="800"
             placement="left"
             class="aoat-inline-block"
             trigger="click">
        <template v-slot:trigger>
          <button>
            <span class="dashicons dashicons-arrow-down-alt2"></span>
          </button>
        </template>
        <div class="aoat-text-left w-800 aoat-py-4">
          <table class="table aoat-w-full">
            <tbody>
            <tr>
              <th>Label:</th>
              <td><input  v-model="object.reportLabel" type="text"></td>
            </tr>
            <tr v-if="typeof object.hidden !== 'undefined'">
              <th>Hidden:</th>
              <td><input  v-model="object.hidden" type="checkbox"></td>
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
            </tbody>
          </table>
        </div>
      </tippy>
      <button class="remove-button" @click="remove()">
        <span class="dashicons dashicons-trash"></span>
      </button>
    </div>
    <span class="handle dashicons dashicons-move" :class="getHandleClass()"></span>
  </div>
</template>

<script>

  import {Multiselect} from "vue-multiselect";

  export default {

    name: 'CommonOptionsReport',

    components: {
      Multiselect
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
    },

    data () {
      return {
        show: false,
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
        ]
      };
    },

    methods: {
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
</style>
