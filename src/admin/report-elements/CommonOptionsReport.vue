<template>
  <div>
    <div class="more-options-button">
      <tippy
        arrow
        :interactive="true"
        theme="light"
        :max-width="800"
        placement="left"
        class="aoat-inline-block"
        trigger="click"
        @show="checkKeys()"
      >
        <template v-slot:trigger>
          <button class="aoat-px-0 aoat-cursor-pointer">
            <span class="dashicons dashicons-admin-generic" />
          </button>
        </template>
        <div class="aoat-text-left w-800 aoat-py-4">
          <table class="table aoat-w-full w-800 aoat-align-top">
            <tbody>
              <tr>
                <th>Label:</th>
                <td><input v-model="object.reportLabel" type="text" /></td>
              </tr>
              <tr v-if="typeof object.hidden !== 'undefined'">
                <th>Hidden:</th>
                <td><input v-model="object.hidden" type="checkbox" /></td>
              </tr>
              <tr v-if="typeof object.excludeForScoreComparing !== 'undefined'">
                <th>Exclude for score compare:</th>
                <td>
                  <input
                    v-model="object.excludeForScoreComparing"
                    type="checkbox"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.currentResult !== 'undefined'">
                <th>Current report:</th>
                <td>
                  <input v-model="object.currentResult" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.previousResult !== 'undefined'">
                <th>Previous report:</th>
                <td>
                  <input v-model="object.previousResult" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.firstResult !== 'undefined'">
                <th>First report:</th>
                <td><input v-model="object.firstResult" type="checkbox" /></td>
              </tr>
              <tr v-if="typeof object.userResults !== 'undefined'">
                <th>User reports:</th>
                <td>
                  <input v-model="object.userResults" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.countryResults !== 'undefined'">
                <th>Country reports:</th>
                <td>
                  <input v-model="object.countryResults" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.customFieldResults !== 'undefined'">
                <th>Reports by custom field:</th>
                <td>
                  <input v-model="object.customFieldResults" type="checkbox" />
                  <select
                    v-if="object.customFieldResults"
                    v-model="object.customFieldResultsFields"
                    class="aoat-mt-3"
                    :multiple="true"
                  >
                    <option
                      v-for="fieldInForm in fieldsInForm"
                      :key="fieldInForm.reportItemKey"
                      :value="fieldInForm.reportItemKey"
                    >
                      {{ fieldInForm.name }}
                    </option>
                  </select>
                </td>
              </tr>
              <tr v-if="typeof object.allResults !== 'undefined'">
                <th>All reports:</th>
                <td>
                  <input v-model="object.allResults" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.averageResult !== 'undefined'">
                <th>Average report:</th>
                <td>
                  <input v-model="object.averageResult" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.averageUserResult !== 'undefined'">
                <th>Average user report:</th>
                <td>
                  <input v-model="object.averageUserResult" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.averageCountryResult !== 'undefined'">
                <th>Average country report:</th>
                <td>
                  <input
                    v-model="object.averageCountryResult"
                    type="checkbox"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.compareScoringTitleField !== 'undefined'">
                <th>Scoring title field:</th>
                <td>
                  <select
                    v-model="object.compareScoringTitleField"
                    multiple="true"
                  >
                    <option
                      v-for="fieldInForm in fieldsInForm"
                      :key="fieldInForm.reportItemKey"
                      :value="fieldInForm.reportItemKey"
                    >
                      {{ fieldInForm.name }}
                    </option>
                  </select>
                </td>
              </tr>
              <tr v-if="typeof object.hideLabels !== 'undefined'">
                <th>Hide labels:</th>
                <td><input v-model="object.hideLabels" type="checkbox" /></td>
              </tr>
              <tr>
                <th>Classes:</th>
                <td><input v-model="object.class" type="text" /></td>
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
                    :options="availableGraphs"
                  />
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
                    :options="availableResultTypes"
                  />
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
                    :options="availableScores"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.scoreGraphColor !== 'undefined'">
                <th>Select score graph color</th>
                <td>
                  <v-swatches
                    v-model="object.scoreGraphColor"
                    :swatches="swatches"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.height !== 'undefined'">
                <th>Max height:</th>
                <td><input v-model="object.height" type="number" /></td>
                <td>
                  <select v-model="object.heightUnit">
                    <option value="px">px</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </tippy>
      <button class="aoat-px-0 aoat-cursor-pointer" @click="toggleShow()">
        <span
          v-if="object.hideInForm"
          class="dashicons aoat-text-orange-600 dashicons-visibility"
        />
        <span v-else class="dashicons dashicons-hidden" />
      </button>
      <tippy
        ref="remove_element"
        arrow
        :interactive="true"
        theme="light"
        :max-width="800"
        placement="left"
        class="aoat-inline-block"
        trigger="click"
        @show="checkKeys()"
      >
        <template v-slot:trigger>
          <button class="aoat-px-0 aoat-cursor-pointer">
            <span class="dashicons dashicons-trash" />
          </button>
        </template>
        <div>Are you sure?</div>
        <button class="aoat-mt-2" @click="remove()">Confirm</button>
      </tippy>
    </div>
    <span class="handle dashicons dashicons-move" :class="getHandleClass()" />
  </div>
</template>

<script>
import { Multiselect } from "vue-multiselect";
import formElements from "../utils/form-elements.js";
import VSwatches from "vue-swatches";
import "vue-swatches/dist/vue-swatches.css";

export default {
  name: "CommonOptionsReport",

  components: {
    Multiselect,
    VSwatches
  },

  props: {
    object: {
      type: Object,
      required: true
    },
    depth: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      swatches: this.$store.state.settings.available_colors,
      availableGraphs: [
        {
          label: "Pie",
          key: "pie"
        },
        {
          label: "Radar",
          key: "radar"
        },
        {
          label: "Grid",
          key: "grid"
        }
      ],
      availableResultTypes: [
        {
          label: "Score",
          key: "score"
        },
        {
          label: "Pie",
          key: "pie"
        },
        {
          label: "Radar",
          key: "radar"
        },
        {
          label: "Bar",
          key: "bar"
        },
        {
          label: "Horizontal bar",
          key: "horizontal_bar"
        }
      ]
    };
  },

  computed: {
    availableScores() {
      return this.getItemsRecursive(
        this.$store.state.report.items
      ).filter(field =>
        ["part_score", "total_score", "compare_score"].includes(field.type)
      );
    },
    fieldsInForm() {
      let childrenKeys = this.getItemsRecursive([this.object]).map(
        item => item.key
      );
      return this.getItemsRecursive(this.$store.state.report.items)
        .filter(field => !childrenKeys.includes(field.key))
        .filter(field =>
          ["text", "select", "date", "radio", "radio_grid"].includes(field.type)
        );
    }
  },

  methods: {
    getItemsRecursive(items) {
      for (let item of items) {
        if (item.items) {
          items = items.concat(this.getItemsRecursive(item.items));
        }
      }
      return items;
    },
    remove() {
      this.$store.dispatch("removeFieldReport", this.object.key);
    },
    getHandleClass() {
      if (this.object.type === "page") {
        return "handle-page" + this.depth;
      }
      if (this.object.type === "column") {
        return "handle-column" + this.depth;
      }
      if (this.object.type === "column") {
        return "handle-row" + this.depth;
      }
      return "handle-other" + this.depth;
    },
    toggleShow() {
      this.$set(this.object, "hideInForm", !this.object.hideInForm);
    },
    checkKeys() {
      let element = formElements.find(el => el.type === this.object.type);

      Object.keys(element).forEach(key => {
        if (typeof this.object[key] === "undefined") {
          this.$set(this.object, key, element[key]);
        }
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
  vertical-align: top;
  text-align: right;
}
/deep/ .multiselect__input {
  display: none;
}
.table input:not([type="checkbox"]),
.table select {
  width: 100%;
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
