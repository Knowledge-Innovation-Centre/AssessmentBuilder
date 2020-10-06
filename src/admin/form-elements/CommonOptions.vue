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
            <tr>
              <th>Name:</th>
              <td><input v-model="object.name" class="aoat-w-full" type="text"></td>
            </tr>
            <tr v-if="typeof object.placeholder !== 'undefined'">
              <th>Placeholder:</th>
              <td><input v-model="object.placeholder" type="text"></td>
            </tr>
            <tr v-if="typeof object.required !== 'undefined'">
              <th>Required:</th>
              <td><input  v-model="object.required" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.hidden !== 'undefined'">
              <th>Hidden:</th>
              <td><input  v-model="object.hidden" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.showTitle !== 'undefined'">
              <th>Show title:</th>
              <td><input  v-model="object.showTitle" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.includeInAssessmentTitle !== 'undefined'">
              <th>Include in assessment title:</th>
              <td><input v-model="object.includeInAssessmentTitle" type="checkbox"></td>
            </tr>
            <tr>
              <th>Show if:</th>
              <td>
                  <table class="">
                  <tr v-for="(condition, index) in object.conditions">

                    <td>
                      <select v-model="condition.field">
                        <option v-for="fieldInForm in fieldsInForm" :key="fieldInForm.key" :value="fieldInForm.key">
                          {{fieldInForm.name}}
                        </option>
                      </select>
                    </td>
                    <template v-if="condition.field">
                      <td v-if="isSelect(getFieldByKey(condition.field))">
                        <select multiple v-model="condition.selectedOptions">
                          <option v-for="option in getFieldByKey(condition.field).options" :key="option.id" :value="option.id">
                            {{option.name}}
                          </option>
                        </select>
                      </td>
                      <td v-if="isRadioGrid(getFieldByKey(condition.field))">
                        <select v-model="condition.question">
                          <option v-for="option in getFieldByKey(condition.field).optionsHorizontal"
                                  :key="option.id"
                                  :value="option.id">
                            {{option.name}}
                          </option>
                        </select>

                      </td>
                      <td v-else>
                        <input v-model="condition.value" type="text" >
                      </td>
                      <td v-if="condition.question">
                        <multiselect
                            v-model="condition.selectedOptions"
                            :multiple="true"
                            label="name"
                            placeholder="Select one"
                            track-by="id"
                            :options="getFieldByKey(condition.field).optionsVertical">
                        </multiselect>

                      </td>

                      <button  @click="removeCondition(index)">X</button>
                    </template>
                  </tr>
                  </table>

              </td>
            </tr>

            <tr>
              <td></td>
              <td><button @click="addCondition()">+</button></td>
            </tr>
            <tr>
              <th>Classes:</th>
              <td><input v-model="object.class" type="text"></td>
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
  import isEmpty from "lodash/isEmpty";

  import "tippy.js/themes/light.css";
  import "tippy.js/themes/light-border.css";

  export default {

    name: 'CommonOptions',

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
      fieldsInForm() {
        let childrenKeys = this.getItemsRecursive([this.object]).map(item => item.key)
        return this.getItemsRecursive(this.$store.state.form.items)
                      .filter(field => !childrenKeys.includes(field.key) )
                      .filter(field => ['text','select','date','radio','radio_grid'].includes(field.type))

      }
    },

    data () {
      return {
        show: false,
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
      getFieldByKey(key) {
        return this.fieldsInForm.find(fieldInForm => fieldInForm.key === key)
      },
      isSelect(field) {
        return field.type === 'select' || field.type === 'radio'
      },
      isRadioGrid(field) {
        return field.type === 'radio_grid'
      },
      getRadioGridQuestions(field) {
        return field.type === 'radio_grid'
      },
      remove() {

        if(!isEmpty(this.$store.state.report)) {
          this.$store.dispatch('removeFieldReport', this.object.key);
        } else {
          this.$store.dispatch('removeField', this.object.key);
        }
      },
      addCondition() {
        this.object.conditions.push({
          field: null,
          question: null,
          selectedOptions: [],
          value: null,
        })
      },
      removeCondition(index) {
        this.object.conditions.splice(index, 1);
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
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
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
