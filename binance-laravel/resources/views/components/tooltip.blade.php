<span {{$attributes->merge(['class' => 'tooltiptext bg-gray-700'])}}>{{ $slot }}</span>

<style>


    .tooltip ~ .tooltiptext {
        visibility: hidden;
        width: auto;
        color: #fff;
        font-size: 10px;
        text-align: center;
        border-radius: 6px;
        padding: 5px 10px;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .tooltip:hover ~ .tooltiptext {
        visibility: visible;
    }
</style>
