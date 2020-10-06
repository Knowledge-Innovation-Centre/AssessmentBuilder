<template>
  <div>

    <div class="more-options-button">
      <tippy arrow
             :interactive="true"
             theme="light"
             :max-width="800"
             class="aoat-inline-block"
             trigger="click">
        <template v-slot:trigger>
          <button>
            <span class="dashicons dashicons-arrow-down-alt2"></span>
          </button>
        </template>
        <div class="aoat-text-left">
        <table class="table aoat-w-full">
          <tbody>
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
              <select v-model="object.selectedGraph">
                <option v-for="availableGraph in availableGraphs"
                        :key="availableGraph.key"
                        :value="availableGraph.key">
                  {{ availableGraph.label }}
                </option>
              </select>
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

  export default {

    name: 'CommonOptionsReport',

    components: {
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
            label: 'Both',
            key: 'both'
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
