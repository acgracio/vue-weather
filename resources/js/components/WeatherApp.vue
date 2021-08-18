<template>
  <div class="text-white mb-8">
    <div class="places-input text-gray-800">
      <!-- <input type="text" class="w-full" /> -->

      <!-- algolia -->
      <input
        type="search"
        id="address"
        class="form-control"
        placeholder="Where are we going?"
      />

      <p>Selected: <strong id="address-value">none</strong></p>
      <!-- <script>
        (function () {})();
      </script> -->
      <!-- end algolia -->
    </div>
    <div
      class="weather-container font-sans md:w-128 max-w-lg rounded-lg overflow-hidden bg-gray-900 shadow-lg mt-4"
    >
      <div class="current-weather flex items-center justify-between px-6 py-8">
        <div class="flex flex-col md:flex-row items-center">
          <div>
            <!-- <div class="text-6xl font-semibold">8°C</div> -->
            <div class="text-6xl font-semibold">
              {{ currentTemperature.actual }}ºC
            </div>
            <div>Feels like {{ currentTemperature.feelsLike }}ºC</div>
          </div>
          <div class="md:mx-5">
            <div class="font-semibold">
              {{ this.currentTemperature.summary | uppercase }}
            </div>
            <!-- <div>{{ location.name }}, {{ location.country }}</div> -->
            <div>{{ location.name }}</div>
          </div>
        </div>
        <div>
          <img :src="currentTemperature.icon" />
        </div>
      </div>
      <!-- end current-weather -->

      <div class="future-weather text-sm bg-gray-800 px-6 py-8 overflow-hidden">
        <div
          v-for="(day, index) in daily"
          :key="day.dt"
          :class="{ 'mt-8': index > 0 }"
          class="flex items-center"
        >
          <div class="w-1/6 text-lg text-gray-200">{{ toWeekDay(day.dt) }}</div>
          <div class="w-2/3 px-4 flex items-center">
            <div>
              <img :src="forecastIcon(day.weather[0].icon)" width="70px" />
            </div>
            <div class="ml-3">{{ day.weather[0].description | uppercase }}</div>
          </div>
          <div class="w-1/6 text-right">
            <div>{{ Math.round(day.temp.max) }}°C</div>
            <div>{{ Math.round(day.temp.min) }}°C</div>
          </div>
        </div>
      </div>

      <!-- end future-weather -->
    </div>
    <!-- end weather-container -->
  </div>
</template>
<script>
export default {
  mounted() {
    this.fetchData();

    // algolia
    var placesAutocomplete = places({
      appId: "plVXKLV5GWPG",
      apiKey: "62ac411694a472c6589c95bd0d8b4869",
      container: document.querySelector("#address"),
    }).configure({
      type: "city",
      aroundLatLngViaIP: false,
    });

    var $address = document.querySelector("#address-value");
    placesAutocomplete.on("change", (e) => {
      $address.textContent = e.suggestion.value;
      console.log(e);
      this.location.name = `${e.suggestion.name}, ${e.suggestion.country}`;
      this.location.lat = e.suggestion.latlng.lat;
      this.location.long = e.suggestion.latlng.lng;
    });

    placesAutocomplete.on("clear", function () {
      $address.textContent = "none";
    });

    // end algolia
  },

  data() {
    return {
      currentTemperature: {
        actual: "",
        feels: "",
        summary: "",
        icon: "",
      },
      location: {
        name: "",
        //leiria
        lat: 39.7443,
        long: -8.8073,
        //pisoes
        //lat: 39.6569,
        //long: -8.9452,
      },
      daily: [],
    };
  },
  //watcher to update the data
  watch: {
    location: {
      handler(newValue, oldValue) {
        this.fetchData();
      },
      //if anything changes
      deep: true,
    },
  },
  methods: {
    fetchData() {
      fetch(`/api/weather?lat=${this.location.lat}&long=${this.location.long}`)
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
          this.location.country = data.sys.country;
          this.currentTemperature.actual = Math.round(data.main.temp);
          this.currentTemperature.feelsLike = Math.round(data.main.feels_like);
          (this.currentTemperature.summary = data.weather[0].description),
            (this.currentTemperature.icon = `http://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`),
            (this.location.name = data.name);
        });
      //daily data
      fetch(`/api/forecast?lat=${this.location.lat}&long=${this.location.long}`)
        .then((res) => res.json())
        .then((data) => {
          this.daily = data.daily;
        });
    },
    toWeekDay(timestamp) {
      const newDate = new Date(timestamp * 1000);
      const days = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
      return days[newDate.getDay()];
    },
    forecastIcon(icon) {
      let iconId = icon;
      return `http://openweathermap.org/img/wn/${iconId}@2x.png`;
    },
  },
};
</script>