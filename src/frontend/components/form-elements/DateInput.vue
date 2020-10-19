<template>
  <div>
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <date-picker
        :class="hasError ? 'aoat-border-red-400' : ''"
        v-model="localDate"
        :style="getWidthStyle"
        :placeholder="object.placeholder"></date-picker>
  </div>
</template>

<script>

import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

  export default {

    name: 'TextInput',

    components: {
      DatePicker
    },

    props: {
      object: {
        type: Object,
        required: true,
      },
      hasError: {
        type: Boolean,
        required: false,
        default: false,
      }
    },

    data () {
      return {
        localDate: null
      };
    },

    computed: {
      getWidthStyle() {
        if (this.object.maxWidth) {
          return "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ';'
        }
      }
    },

    watch: {
      localDate() {
        this.$store.dispatch('updateValue', { key: this.object.key, value: this.localDate})
      }
    },


    methods: {
    }
  };
</script>
