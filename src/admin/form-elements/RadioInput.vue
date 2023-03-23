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
          <th>Score</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(option, index) in object.options" :key="option.id">
          <td class="aoat-w-32">
            <input v-model="option.id" class="aoat-w-full" type="text" />
          </td>
          <td>
            <input v-model="option.name" class="aoat-w-full" type="text" />
          </td>
          <td class="aoat-w-12">
            <input v-model="option.score" class="aoat-w-full" type="number" />
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
            <tippy
              ref="import"
              :interactive="true"
              :max-width="800"
              arrow
              class="aoat-inline-block"
              placement="left"
              theme="light"
              trigger="click"
            >
              <template v-slot:trigger>
                <button class="aoat-px-0 aoat-cursor-pointer">
                  Import options
                </button>
              </template>
              <div>
                Paste comma separated values (id, value, score). Each line is
                new option
              </div>
              <textarea v-model="pastedOptions" />
              <div>Delimiter</div>
              <input v-model="delimiter" />
              <div>
                <button class="aoat-mt-2" @click="addOptions()">
                  Import options
                </button>
              </div>
            </tippy>
            <tippy
              ref="remove_element"
              :interactive="true"
              :max-width="800"
              arrow
              class="aoat-inline-block"
              placement="left"
              theme="light"
              trigger="click"
            >
              <template v-slot:trigger>
                <button class="aoat-px-0 aoat-cursor-pointer">
                  Clear all options
                </button>
              </template>
              <div>Are you sure?</div>
              <button class="aoat-mt-2" @click="removeOptions()">
                Confirm
              </button>
            </tippy>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
export default {
  name: "RadioInput",

  components: {},

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      show: false,
      pastedOptions: "",
      delimiter: ","
    };
  },
  methods: {
    addOption() {
      this.object.options.push({
        id: "",
        name: "",
        score: 1,
        color: "#E84B3C"
      });
    },
    addOptions() {
      const lines = this.pastedOptions.split(/\r?\n/);

      for (const line of lines) {
        const values = line.split(this.delimiter);
        if (values.length < 2) {
          continue;
        }
        this.object.options.push({
          id: values[0].trim(),
          name: values[1].trim(),
          score: parseInt((values[2] ?? "1").trim()),
          color: "#E84B3C"
        });
      }
    },
    removeOptions() {
      this.object.options = [];
    },
    removeOption(index) {
      this.object.options.splice(index, 1);
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
