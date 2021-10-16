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
          <button class=" aoat-px-0 aoat-cursor-pointer">
            <span class="dashicons dashicons-admin-generic" />
          </button>
        </template>

        <div class="aoat-text-left w-800 aoat-py-4">
          <table class="table aoat-w-full  w-800">
            <tbody>
              <tr>
                <th style="width: 100px;">Name:</th>
                <td colspan="2">
                  <input
                    v-model="object.name"
                    class="aoat-w-full"
                    type="text"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.placeholder !== 'undefined'">
                <th>Placeholder:</th>
                <td colspan="2">
                  <input v-model="object.placeholder" type="text" />
                </td>
              </tr>
              <tr v-if="typeof object.required !== 'undefined'">
                <th>Required:</th>
                <td colspan="2">
                  <input v-model="object.required" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.hidden !== 'undefined'">
                <th>Hidden:</th>
                <td colspan="2">
                  <input v-model="object.hidden" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.multiple !== 'undefined'">
                <th>Multiple:</th>
                <td colspan="2">
                  <input v-model="object.multiple" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.showTitle !== 'undefined'">
                <th>Show title:</th>
                <td colspan="2">
                  <input v-model="object.showTitle" type="checkbox" />
                </td>
              </tr>
              <tr v-if="typeof object.includeInAssessmentTitle !== 'undefined'">
                <th>Include in assessment title:</th>
                <td colspan="2">
                  <input
                    v-model="object.includeInAssessmentTitle"
                    type="checkbox"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.disableForScoring !== 'undefined'">
                <th>Disable for scoring:</th>
                <td colspan="2">
                  <input v-model="object.disableForScoring" type="checkbox" />
                </td>
              </tr>
              <tr>
                <th>Show if:</th>
                <td colspan="2">
                  <table class="aoat-w-full">
                    <tr v-for="(condition, index) in conditions">
                      <td v-if="condition">
                        <select v-model="condition.field">
                          <option
                            v-for="fieldInForm in fieldsInForm"
                            :key="fieldInForm.key"
                            :value="fieldInForm.key"
                          >
                            {{ fieldInForm.name }}
                          </option>
                        </select>
                      </td>
                      <template v-if="condition && condition.field">
                        <td v-if="isSelect(getFieldByKey(condition.field))">
                          <multiselect
                            v-model="condition.selectedOptions"
                            :multiple="true"
                            label="name"
                            placeholder="Select one"
                            class="aoat-w-full"
                            track-by="id"
                            :allow-empty="false"
                            :options="getFieldByKey(condition.field).options"
                          />
                        </td>
                        <template
                          v-else-if="
                            isRadioGrid(getFieldByKey(condition.field))
                          "
                        >
                          <td>
                            <select v-model="condition.question">
                              <option
                                v-for="option in getFieldByKey(condition.field)
                                  .optionsHorizontal"
                                :key="option.id"
                                :value="option.id"
                              >
                                {{ option.name }}
                              </option>
                            </select>
                          </td>
                          <td v-if="condition.question">
                            <multiselect
                              v-model="condition.selectedOptions"
                              :multiple="true"
                              label="name"
                              placeholder="Select one"
                              track-by="id"
                              :allow-empty="false"
                              :options="
                                getFieldByKey(condition.field).optionsVertical
                              "
                            />
                          </td>
                        </template>
                        <td v-else>
                          <input v-model="condition.value" type="text" />
                        </td>
                      </template>
                      <td style="width: 20px; min-width: 20px;">
                        <button @click="removeCondition(index)">X</button>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <tr>
                <th />
                <td colspan="2"><button @click="addCondition()">+</button></td>
              </tr>
              <tr>
                <th>Classes:</th>
                <td colspan="2">
                  <input v-model="object.class" type="text" />
                </td>
              </tr>
              <tr>
                <th>Max width:</th>
                <td><input v-model="object.maxWidth" type="number" /></td>
                <td>
                  <select v-model="object.maxWidthUnit">
                    <option value="px">px</option>
                    <option value="%">%</option>
                  </select>
                </td>
              </tr>
              <tr v-if="typeof object.maxSize !== 'undefined'">
                <th>Max size (MB):</th>
                <td colspan="2">
                  <input v-model="object.maxSize" type="number" />
                </td>
              </tr>
              <tr v-if="typeof object.maxFiles !== 'undefined'">
                <th>Max files:</th>
                <td colspan="2">
                  <input v-model="object.maxFiles" type="number" />
                </td>
              </tr>
              <tr v-if="typeof object.acceptedFileTypes !== 'undefined'">
                <th>Accepted file types:</th>
                <td colspan="2">
                  <multiselect
                    v-model="object.acceptedFileTypes"
                    :multiple="true"
                    placeholder="Select one"
                    :close-on-select="false"
                    :options="fileTypes"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.roleConditions !== 'undefined'">
                <th>Select roles:</th>
                <td colspan="2">
                  <multiselect
                    v-model="object.roleConditions"
                    placeholder="Select one"
                    :close-on-select="true"
                    :options="object.options"
                  />
                </td>
              </tr>

              <tr v-if="typeof object.queryParameterField !== 'undefined'">
                <th>Query parameter key:</th>
                <td colspan="2">
                  <input v-model="object.queryParameterField" type="text" />
                </td>
              </tr>
              <tr v-if="typeof object.labelParts !== 'undefined'">
                <th>Build label:</th>
                <td colspan="2">
                  <multiselect
                    v-model="object.labelParts"
                    :multiple="true"
                    placeholder="Select one"
                    label="name"
                    track-by="key"
                    :allow-empty="false"
                    :options="countryLabelParts"
                  />
                </td>
              </tr>
              <tr v-if="typeof object.labelPartsSeperator !== 'undefined'">
                <th>Label seperator:</th>
                <td colspan="2">
                  <input v-model="object.labelPartsSeperator" type="text" />
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
import isEmpty from "lodash/isEmpty";

import "tippy.js/themes/light.css";
import "tippy.js/themes/light-border.css";
import formElements from "../utils/form-elements";

export default {
  name: "CommonOptions",

  components: {
    Multiselect
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
      fileTypes: [
        "application/pdf",
        "image/jpeg",
        "image/png",
        "application/vnd.ms-excel",
        "application/xml",
        "text/xml",
        "text/plain",
        "audio/webm",
        "video/webm",
        "audio/mpeg",
        "video/mpeg",
        "text/csv",
        "application/zip"
      ],
      countryLabelParts: [
        {
          name: "Country name",
          key: "name"
        },
        {
          name: "Country code",
          key: "id"
        }
      ]
    };
  },

  computed: {
    fieldsInForm() {
      let childrenKeys = this.getItemsRecursive([this.object]).map(
        item => item.key
      );
      return this.getItemsRecursive(this.$store.state.form.items)
        .filter(field => !childrenKeys.includes(field.key))
        .filter(field =>
          ["text", "select", "date", "radio", "radio_grid"].includes(field.type)
        );
    },
    conditions() {
      if (!this.object.conditions) {
        return [];
      }
      return this.object.conditions;
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
    getFieldByKey(key) {
      return this.fieldsInForm.find(fieldInForm => fieldInForm.key === key);
    },
    isSelect(field) {
      return field.type === "select" || field.type === "radio";
    },
    isRadioGrid(field) {
      return field.type === "radio_grid";
    },
    getRadioGridQuestions(field) {
      return field.type === "radio_grid";
    },
    remove() {
      if (!isEmpty(this.$store.state.report)) {
        this.$store.dispatch("removeFieldReport", this.object.key);
      } else {
        this.$store.dispatch("removeField", this.object.key);
      }
    },
    addCondition() {
      this.object.conditions.push({
        field: null,
        question: null,
        selectedOptions: [],
        value: null
      });
    },
    removeCondition(index) {
      this.object.conditions.splice(index, 1);
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
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
.table > tbody > tr > th,
.table > tbody > tr > td {
  padding: 5px 10px;
}
.table > tbody > tr > th {
  text-align: right;
  vertical-align: top;
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
.w-800 {
  width: 800px;
}
td {
  max-width: 250px;
  min-width: 200px;
}
select {
  width: 100%;
}
</style>
