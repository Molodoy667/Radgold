@extends('admin.settings.setting-layout')
@section('title')
    {{ __('theme_setting') }}
@endsection

@section('website-settings')
    <div class="alert alert-warning mb-3">
        <h5>{{ __('heads_up_customize_the_way_you_like') }}</h5>
        <hr class="my-2">
        {{ __('add_your_brand_theme_color_it_will_be_reflected_on_your_website_and_admin_panel_add_your') }} <a
            href="{{ route('settings.general') }}" class="text-info">{{ __('logo_and_favicon_here') }}</a>.
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="line-height: 36px;">{{ __('Website landing Page') }}</h3>
        </div>
        <form action="{{ route('settings.homepage.theme.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="row col-md-6 justify-content-start">
                        <div class="col-md-4">
                            <label class="image-container">
                                <input type="radio" value="1" name="current_theme" id="1"
                                    {{ old('current_theme', $setting->current_theme) == 1 ? 'checked' : '' }}>
                                <img class="w-100" src="{{ asset('backend/image/home-01.webp') }}" alt="">
                                <span class="checked-badge"></span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="image-container">
                                <input type="radio" value="2" name="current_theme" id="2"
                                    {{ old('current_theme', $setting->current_theme) == 2 ? 'checked' : '' }}>
                                <img class="w-100" src="{{ asset('backend/image/home-02.webp') }}" alt="">
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="image-container">
                                <input type="radio" name="current_theme" value="3" id="3"
                                    {{ old('current_theme', $setting->current_theme) == 3 ? 'checked' : '' }}>
                                <img class="w-100" src="{{ asset('backend/image/home-03.webp') }}" alt="">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            @if (userCan('setting.update'))
                <div class="card-footer text-center">
                    <button style="width: 250px;" onclick="$('#color_picker_form').submit()" type="submit"
                        class="btn btn-primary">{{ __('update') }}</button>
                </div>
            @endif
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="line-height: 36px;">{{ __('website_theme_style') }}</h3>
        </div>
        <div class="card-body">
            <!-- Primary Gradient Selector -->
            <div class="mb-4">
                <h5 class="mb-3">{{ __('primary_gradient') }} ({{ __('main_site_gradient') }})</h5>
                <p class="text-muted small">Current: {{ $setting->frontend_primary_color ?? 'Not set' }}</p>
                <div class="gradient-selector" data-target="frontend_primary_color">
                    <div class="row">
                        @php
                        $primaryGradients = [
                            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                            'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                            'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                            'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                            'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)',
                            'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
                            'linear-gradient(135deg, #ff8a80 0%, #ea4c89 100%)',
                            'linear-gradient(135deg, #8fd3f4 0%, #84fab0 100%)',
                            'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
                            'linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%)',
                            'linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%)',
                            'linear-gradient(135deg, #2196f3 0%, #21cbf3 100%)',
                            'linear-gradient(135deg, #4568dc 0%, #b06ab3 100%)',
                            'linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%)',
                            'linear-gradient(135deg, #42275a 0%, #734b6d 100%)',
                            'linear-gradient(135deg, #f8cdda 0%, #1d2b64 100%)',
                            'linear-gradient(135deg, #e96443 0%, #904e95 100%)',
                            'linear-gradient(135deg, #24fe41 0%, #fdbb2d 100%)',
                            'linear-gradient(135deg, #df89b5 0%, #bfd9fe 100%)',
                            'linear-gradient(135deg, #ed6ea0 0%, #ec8c69 100%)',
                            'linear-gradient(135deg, #74b9ff 0%, #0984e3 100%)',
                            'linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%)',
                            'linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%)',
                            'linear-gradient(135deg, #fd7f6f 0%, #7b68ee 100%)',
                            'linear-gradient(135deg, #ffb3fd 0%, #8ec5fc 100%)',
                            'linear-gradient(135deg, #96fbc4 0%, #f9f047 100%)'
                        ];
                        @endphp
                        
                        @foreach($primaryGradients as $index => $gradient)
                        @php
                            $isSelected = ($setting->frontend_primary_color ?? '') == $gradient;
                            // If no gradient is set, select the first one by default
                            if (!$setting->frontend_primary_color && $index == 0) {
                                $isSelected = true;
                            }
                        @endphp
                        <div class="col-md-2 col-sm-3 col-4 mb-3">
                            <div class="gradient-cube {{ $isSelected ? 'selected' : '' }}" 
                                 data-gradient="{{ $gradient }}"
                                 style="background: {{ $gradient }};"
                                 title="Primary Gradient {{ $index + 1 }}">
                                @if($isSelected)
                                    <i class="fas fa-check text-white"></i>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Secondary Gradient Selector -->
            <div class="mb-4">
                <h5 class="mb-3">{{ __('secondary_gradient') }} ({{ __('top_bar_bottom_nav_gradient') }})</h5>
                <p class="text-muted small">Current: {{ $setting->frontend_secondary_color ?? 'Not set' }}</p>
                <div class="gradient-selector" data-target="frontend_secondary_color">
                    <div class="row">
                        @php
                        $secondaryGradients = [
                            'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
                            'linear-gradient(135deg, #2c3e50 0%, #3498db 100%)',
                            'linear-gradient(135deg, #141e30 0%, #243b55 100%)',
                            'linear-gradient(135deg, #000428 0%, #004e92 100%)',
                            'linear-gradient(135deg, #203a43 0%, #2c5364 100%)',
                            'linear-gradient(135deg, #0f0c29 0%, #302b63 100%)',
                            'linear-gradient(135deg, #24243e 0%, #302b63 100%)',
                            'linear-gradient(135deg, #29323c 0%, #485563 100%)',
                            'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                            'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)',
                            'linear-gradient(135deg, #0f3460 0%, #0d47a1 100%)',
                            'linear-gradient(135deg, #1565c0 0%, #0277bd 100%)',
                            'linear-gradient(135deg, #263238 0%, #37474f 100%)',
                            'linear-gradient(135deg, #212121 0%, #424242 100%)',
                            'linear-gradient(135deg, #1b1b2f 0%, #162447 100%)',
                            'linear-gradient(135deg, #2980b9 0%, #6bb6ff 100%)',
                            'linear-gradient(135deg, #134e5e 0%, #71b280 100%)',
                            'linear-gradient(135deg, #005c98 0%, #505bda 100%)',
                            'linear-gradient(135deg, #833ab4 0%, #fd1d1d 100%)',
                            'linear-gradient(135deg, #667db6 0%, #0082c8 100%)',
                            'linear-gradient(135deg, #2196f3 0%, #1976d2 100%)',
                            'linear-gradient(135deg, #3f51b5 0%, #2196f3 100%)',
                            'linear-gradient(135deg, #9c27b0 0%, #673ab7 100%)',
                            'linear-gradient(135deg, #607d8b 0%, #455a64 100%)',
                            'linear-gradient(135deg, #37474f 0%, #263238 100%)',
                            'linear-gradient(135deg, #1e88e5 0%, #1565c0 100%)',
                            'linear-gradient(135deg, #424242 0%, #212121 100%)',
                            'linear-gradient(135deg, #546e7a 0%, #37474f 100%)',
                            'linear-gradient(135deg, #5e35b1 0%, #512da8 100%)',
                            'linear-gradient(135deg, #1976d2 0%, #0d47a1 100%)'
                        ];
                        @endphp
                        
                        @foreach($secondaryGradients as $index => $gradient)
                        @php
                            $isSelected = ($setting->frontend_secondary_color ?? '') == $gradient;
                            // If no gradient is set, select the first one by default
                            if (!$setting->frontend_secondary_color && $index == 0) {
                                $isSelected = true;
                            }
                        @endphp
                        <div class="col-md-2 col-sm-3 col-4 mb-3">
                            <div class="gradient-cube {{ $isSelected ? 'selected' : '' }}" 
                                 data-gradient="{{ $gradient }}"
                                 style="background: {{ $gradient }};"
                                 title="Secondary Gradient {{ $index + 1 }}">
                                @if($isSelected)
                                    <i class="fas fa-check text-white"></i>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if (userCan('setting.update'))
            <div class="card-footer text-center">
                <button style="width: 250px;" onclick="$('#color_picker_form').submit()" type="submit"
                    class="btn btn-primary">{{ __('update') }}</button>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">{{ __('admin_theme_style') }}</h3>
                </div>
                <div class="px-4 pt-3 pb-4">
                    <form id="color_picker_form" action="{{ route('settings.theme.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <input id="sidebar_color_id" type="hidden" name="sidebar_color"
                            value="{{ $setting->sidebar_color }}">
                        <input id="nav_color_id" type="hidden" name="nav_color" value="{{ $setting->nav_color }}">
                        <input id="sidebar_txt_color_id" type="hidden" name="sidebar_txt_color"
                            value="{{ $setting->sidebar_txt_color }}">
                        <input id="nav_txt_color_id" type="hidden" name="nav_txt_color"
                            value="{{ $setting->nav_txt_color }}">
                        <input id="main_color_id" type="hidden" name="main_color" value="{{ $setting->main_color }}">
                        <input id="accent_color_id" type="hidden" name="accent_color"
                            value="{{ $setting->accent_color }}">
                        <input id="frontend_primary_color_id" type="hidden" name="frontend_primary_color"
                            value="{{ $setting->frontend_primary_color ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }}">
                        <input id="frontend_secondary_color_id" type="hidden" name="frontend_secondary_color"
                            value="{{ $setting->frontend_secondary_color ?? 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)' }}">
                    </form>
                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">{{ __('left_sidebar_background_color') }}</div>
                                <div class="card-body">
                                    <div class="sidebar-bg-color-picker"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">{{ __('left_sidebar_text_color') }}</div>
                                <div class="card-body">
                                    <div class="sidebar-text-color-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">{{ __('top_nav_background_color') }}</div>
                                <div class="card-body">
                                    <div class="navbar-bg-color-picker"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">{{ __('top_nav_text_color') }}</div>
                                <div class="card-body">
                                    <div class="navbar-text-color-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">{{ __('main_color') }}</div>
                                <div class="card-body">
                                    <div class="main-color-picker"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">{{ __('accent_color') }}</div>
                                <div class="card-body">
                                    <div class="accent-color-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (userCan('setting.update'))
                    <div class="card-footer text-center">
                        <button style="width: 250px;" onclick="$('#color_picker_form').submit()" type="submit"
                            class="btn btn-primary">{{ __('update') }}</button>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">{{ __('layout_setting') }} </h3>
                </div>
                <div class="px-4">
                    <div class="row pt-3 pb-4">
                        <form action="{{ route('settings.layout.update') }}" method="post" id="layout_form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="default_layout" id="layout_mode">
                        </form>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0">{{ __('left_navigation_layout') }}</h5>
                                </div>
                                <img style="width: 250px; height: auto"
                                    src="{{ asset('backend/image/setting/left-sidebarlayout.webp') }}"
                                    class="card-img-top img-fluid" alt="top nav">

                                @if (userCan('setting.update'))
                                    <div class="card-body">
                                        @if ($setting->default_layout)
                                            <a href="javascript:void(0)" onclick="layoutChange(0)"
                                                class="btn btn-danger">{{ __('inactivate') }}</a>
                                        @else
                                            <a href="javascript:void(0)" onclick="layoutChange(1)"
                                                class="btn btn-primary">{{ __('activate') }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0">{{ __('top_navigation_layout') }}</h5>
                                </div>
                                <img style="width: 250px; height: auto"
                                    src="{{ asset('backend/image/setting/top-sidebarlayout.webp') }}"
                                    class="card-img-top img-fluid" alt="top nav">
                                @if (userCan('setting.update'))
                                    <div class="card-body">
                                        @if ($setting->default_layout)
                                            <a href="javascript:void(0)" onclick="layoutChange(0)"
                                                class="btn btn-primary">{{ __('activate') }}</a>
                                        @else
                                            <a href="javascript:void(0)" onclick="layoutChange(1)"
                                                class="btn btn-danger">{{ __('inactivate') }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend/plugins/pickr') }}/classic.min.css" />
    <style>
        .image-container {
            height: 250px;
            max-height: 250px;
            overflow: hidden;
            position: relative;
            border: 1px solid #ddd;
            cursor: pointer;
        }

        .image-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-left: 56px solid var(--main-color);
            border-bottom: 32px solid transparent;
            display: none;
        }

        .image-container input {
            display: none;
        }

        .image-container:has(input:checked) {
            border: 2px solid var(--main-color);
        }

        .image-container:has(input:checked)::after {
            display: block;
        }

        .image-container .w-100 {
            width: 100%;
            height: auto;
            transition: transform 2s ease;
        }

        .image-container:hover .w-100 {
            transform: translateY(calc(-100% + 250px));
        }
    </style>
@endsection

@section('style')
<style>
/* Gradient Cube Styles */
.gradient-cube {
    width: 100%;
    height: 60px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 3px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.gradient-cube:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

.gradient-cube.selected {
    border-color: #007bff;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    transform: translateY(-2px);
}

.gradient-cube.selected::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-radius: 15px;
    z-index: -1;
}

.gradient-cube i {
    font-size: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    z-index: 2;
}

.gradient-selector h5 {
    color: #495057;
    font-weight: 600;
}

.gradient-selector {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .gradient-cube {
        height: 50px;
    }
    
    .gradient-cube i {
        font-size: 16px;
    }
}

@media (max-width: 576px) {
    .gradient-cube {
        height: 45px;
    }
    
    .gradient-cube i {
        font-size: 14px;
    }
}
</style>
@endsection

@section('script')
    <script>
        // Gradient Selector Handler
        $(document).ready(function() {
            $('.gradient-cube').on('click', function() {
                const gradient = $(this).data('gradient');
                const selector = $(this).closest('.gradient-selector');
                const target = selector.data('target');
                
                // Remove selected class from siblings
                selector.find('.gradient-cube').removeClass('selected').find('i').remove();
                
                // Add selected class to clicked cube
                $(this).addClass('selected').append('<i class="fas fa-check text-white"></i>');
                
                // Update hidden input
                $(`#${target}_id`).val(gradient);
                
                // Show success message
                toastr.success('Gradient selected! Click Update to save changes.');
            });
        });

        function layoutChange(value) {
            $('#layout_mode').val(value)
            $('#layout_form').submit()
        }
        const colorPickers = [{
                default: '{{ $setting->sidebar_color }}',
                el: ".sidebar-bg-color-picker",
                input: '#sidebar_color_id',
                variable: '--sidebar-bg-color',
            },
            {
                default: '{{ $setting->sidebar_txt_color }}',
                el: ".sidebar-text-color-picker",
                input: '#sidebar_txt_color_id',
                variable: '--sidebar-txt-color',
            },
            {
                el: ".navbar-bg-color-picker",
                default: '{{ $setting->nav_color }}',
                variable: '--top-nav-bg-color',
                input: "#nav_color_id",
            },
            {
                el: ".navbar-text-color-picker",
                default: '{{ $setting->nav_txt_color }}',
                variable: '--top-nav-txt-color',
                input: "#nav_txt_color_id",
            },
            {
                el: ".accent-color-picker",
                default: '{{ $setting->accent_color }}',
                variable: '--accent-color',
                input: "#accent_color_id",
            },
            {
                el: ".main-color-picker",
                default: '{{ $setting->main_color }}',
                variable: '--main-color',
                input: "#main_color_id",
            },
            {
                el: ".frontend-primary-color",
                default: '{{ $setting->frontend_primary_color }}',
                variable: '--frontend-primary-color',
                input: "#frontend_primary_id",
            },
            {
                el: ".frontend-secondary-color",
                default: '{{ $setting->frontend_secondary_color }}',
                variable: '--frontend-secondary-color',
                input: "#frontend_secondary_id",
            },
        ]

        let root = document.documentElement;
        const defaultComponents = {
            preview: true,
            opacity: true,
            hue: true,

            interaction: {
                hex: true,
                rgba: true,
                cmyk: true,
                input: true,
                save: true,
                clear: true,
            }
        }

        colorPickers.forEach(element => {
            const colorPicker = Pickr.create({
                el: element.el,
                theme: "classic",
                default: element.default,
                components: defaultComponents
            });

            colorPicker.on('change', (color, source, instance) => {
                setColor(color.toRGBA().toString(0), null, element.variable, element.input);
            }).on('save', (color, instance) => {
                let colorVal = color ? color.toRGBA().toString(0) : $(element.input).val();
                setColor(colorVal, true, element.variable, element.input);
            });

            function setColor(color, instance, variable, input) {
                root.style.setProperty(variable, color);

                if (instance) {
                    $(input).val(color);
                    colorPicker.hide();
                }
            }
        });
    </script>
@endsection
