<?php

use Jeffreyvr\Paver\Blocks\Options\Input;
use Jeffreyvr\Paver\Blocks\Options\Select;
use Jeffreyvr\Paver\Blocks\Options\Textarea;

describe('Input Option', function () {
    it('creates via make method', function () {
        $input = Input::make('Label', 'fieldName');
        
        expect($input)->toBeInstanceOf(Input::class);
    });

    it('renders input element', function () {
        $input = new Input('Username', 'username');
        $html = $input->render();
        
        expect($html)->toContain('<input');
        expect($html)->toContain('x-model="username"');
        expect($html)->toContain('Username');
    });

    it('includes custom attributes', function () {
        $input = new Input('Email', 'email', ['type' => 'email', 'placeholder' => 'Enter email']);
        $html = $input->render();
        
        expect($html)->toContain('type="email"');
        expect($html)->toContain('placeholder="Enter email"');
    });

    it('can have condition', function () {
        $input = Input::make('Name', 'name')->condition('showName === true');
        $html = (string) $input;
        
        expect($html)->toContain('x-show="showName === true"');
    });
});

describe('Textarea Option', function () {
    it('renders textarea element', function () {
        $textarea = new Textarea('Description', 'description');
        $html = $textarea->render();
        
        expect($html)->toContain('<textarea');
        expect($html)->toContain('x-model="description"');
        expect($html)->toContain('Description');
    });

    it('includes custom attributes', function () {
        $textarea = new Textarea('Bio', 'bio', ['rows' => '5']);
        $html = $textarea->render();
        
        expect($html)->toContain('rows="5"');
    });
});

describe('Select Option', function () {
    it('renders select element', function () {
        $select = new Select('Color', 'color', [
            'red' => 'Red',
            'blue' => 'Blue',
        ]);
        $html = $select->render();
        
        expect($html)->toContain('<select');
        expect($html)->toContain('x-model="color"');
        expect($html)->toContain('Color');
    });

    it('renders options', function () {
        $select = new Select('Size', 'size', [
            'sm' => 'Small',
            'lg' => 'Large',
        ]);
        $html = $select->render();
        
        expect($html)->toContain('<option value="sm">Small</option>');
        expect($html)->toContain('<option value="lg">Large</option>');
    });

    it('includes custom attributes', function () {
        $select = new Select('Type', 'type', ['a' => 'A'], ['class' => 'form-select']);
        $html = $select->render();
        
        expect($html)->toContain('class="form-select"');
    });
});

describe('Option Wrapper', function () {
    it('has default wrapper', function () {
        $input = new Input('Test', 'test');
        
        expect($input->wrapper)->toBe('_INJECT_');
    });

    it('wraps with condition', function () {
        $input = Input::make('Test', 'test')->condition('visible');
        
        expect($input->wrapper)->toContain('x-show="visible"');
    });

    it('casts to string with wrapper', function () {
        $input = Input::make('Test', 'test')->condition('show');
        $html = (string) $input;
        
        expect($html)->toContain('<div x-show="show">');
        expect($html)->toContain('<input');
    });
});
