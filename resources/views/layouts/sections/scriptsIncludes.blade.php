<!-- laravel style -->

<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

<!-- beautify ignore:start -->

@if ($configData['hasCustomizer'])

  <!-- Template customizer -->
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>

@endif

<!-- Config -->
<script src="{{ asset('assets/js/config.js') }}"></script>

@if ($configData['hasCustomizer'])

  <script>
    window.templateCustomizer = new TemplateCustomizer({
      cssPath: '',
      themesPath: '',
      defaultShowDropdownOnHover: {{$configData['showDropdownOnHover']}},
      displayCustomizer: {{$configData['displayCustomizer']}},
      lang: '{{ app()->getLocale() }}',

      pathResolver: function(path) {

        var resolvedPaths = {

          // Core stylesheets
          @foreach (['core'] as $name)
            '{{ $name }}.css': '{{ asset("assets/vendor/css{$configData['rtlSupport']}/{$name}.css") }}',
            '{{ $name }}-dark.css': '{{ asset("assets/vendor/css{$configData['rtlSupport']}/{$name}-dark.css") }}',
          @endforeach

          // Themes
          @foreach (['default', 'bordered', 'semi-dark'] as $name)
            'theme-{{ $name }}.css': '{{ asset("assets/vendor/css{$configData['rtlSupport']}/theme-{$name}.css") }}',
            'theme-{{ $name }}-dark.css': '{{ asset("assets/vendor/css{$configData['rtlSupport']}/theme-{$name}-dark.css") }}',
          @endforeach

        };

        return resolvedPaths[path] || path;
      },

      'controls': <?php echo json_encode($configData['customizerControls']); ?>,
    });
  </script>

@endif

<!-- beautify ignore:end -->

<!-- Google Analytics -->
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }

  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>