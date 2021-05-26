<?php

class MathJaxHooks
{
    public static function onParserFirstCallInit(Parser $parser)
    {
        global $wgOut, $wgMathJaxUseSvg,$wgMathJaxAms, $wgMathJaxSvgOptions, $wgMathJaxCHtmlOptions;

        $wgOut->addJsConfigVars('wgMathJaxUseSvg', $wgMathJaxUseSvg);
        $wgOut->addJsConfigVars('wgMathJaxAms', $wgMathJaxAms);
        $wgOut->addJsConfigVars('wgMathJaxSvgOptions', $wgMathJaxSvgOptions);
        $wgOut->addJsConfigVars('wgMathJaxCHtmlOptions', $wgMathJaxCHtmlOptions);

        $parser->setHook('math', __CLASS__.'::mathRender');
        $parser->setHook('chem', __CLASS__.'::chemRender');
    }

    public static function mathRender($tex, array $args, Parser $parser, PPFrame $frame)
    {
        $tex = str_replace('\>', '\;', $tex);
        $tex = str_replace('<', '\lt ', $tex);
        $tex = str_replace('>', '\gt ', $tex);
        $tex = "\displaystyle{ $tex }";

        if (isset($args['block'])) {
            return self::randerTex($tex, $parser, true);
        }

        return self::randerTex($tex, $parser);
    }

    public static function chemRender($tex, array $args, Parser $parser, PPFrame $frame)
    {
        if (isset($args['block'])) {
            return self::randerTex("\ce{ $tex }", $parser, true);
        }

        return self::randerTex("\ce{ $tex }", $parser);
    }

    private static function randerTex($tex, $parser, bool $isBlock = false)
    {
        $parser->getOutput()->addModules('ext.MathJax');
        $parser->getOutput()->addModules('ext.MathJax.mobile');
        $attributes = ['style' => 'opacity:.5'];

        if ($isBlock) {
            $element = Html::Element('span', $attributes, "[mathblock]{$tex}[/mathblock]");
        } else {
            $element = Html::Element('span', $attributes, "[math]{$tex}[/math]");
        }

        return [$element, 'markerType' => 'nowiki'];
    }
}
