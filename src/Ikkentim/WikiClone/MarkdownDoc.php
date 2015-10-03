<?php namespace Ikkentim\WikiClone;

use Illuminate\Support\Str;
use Michelf\MarkdownExtra;

class MarkdownDoc extends MarkdownExtra {
    protected function doFencedCodeBlocks($text)
    {
        #
        # Adding the fenced code block syntax to regular Markdown:
        #
        # ~~~
        # Code block
        # ~~~
        #
        $less_than_tab = $this->tab_width;

        // Add '#' character to class name for 'c#'
        $text = preg_replace_callback('{
				(?:\n|\A)
				# 1: Opening marker
				(
					(?:~{3,}|`{3,}) # 3 or more tildes/backticks.
				)
				[ ]*
				(?:
					\.?([-_:a-zA-Z0-9#]+) # 2: standalone class name
				|
					' . $this->id_class_attr_catch_re . ' # 3: Extra attributes
				)?
				[ ]* \n # Whitespace and newline following marker.

				# 4: Content
				(
					(?>
						(?!\1 [ ]* \n)	# Not a closing marker.
						.*\n+
					)+
				)

				# Closing marker.
				\1 [ ]* (?= \n )
			}xm',
                                      [$this, '_doFencedCodeBlocks_callback'], $text);

        return $text;
    }

    protected function _doFencedCodeBlocks_callback($matches)
    {
        $classname =& $matches[2];
        $attrs =& $matches[3];
        $codeblock = $matches[4];
        $codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);
        $codeblock = preg_replace_callback('/^\n+/',
                                           [$this, '_doFencedCodeBlocks_newlines'], $codeblock);

        // If no class name specified, use nohighlight
        if ($classname == "")
        {
            $classname = "nohighlight";
        }

        if ($classname{0} == '.')
        {
            $classname = substr($classname, 1);
        }

        // Improve class name detection
        $classname = str_replace('#', 's', strtolower($classname));

        $attr_str = ' class="' . $this->code_class_prefix . $classname . '"';

        $pre_attr_str = $this->code_attr_on_pre ? $attr_str : '';
        $code_attr_str = $this->code_attr_on_pre ? '' : $attr_str;
        $codeblock = "<pre$pre_attr_str><code$code_attr_str>$codeblock</code></pre>";

        return "\n\n" . $this->hashBlock($codeblock) . "\n\n";
    }

    protected function _doHeaders_callback_setext($matches)
    {
        if ($matches[3] == '-' && preg_match('{^- }', $matches[1]))
        {
            return $matches[0];
        }
        $level = $matches[3]{0} == '=' ? 1 : 2;
        $defaultId = is_callable($this->header_id_func) ? call_user_func($this->header_id_func, $matches[1]) : null;
        $attr = $this->doExtraAttributes("h$level", $dummy =& $matches[2], $defaultId);

        if ($level == 2)
        {
            $safeName = Str::slug($this->runSpanGamut($matches[1]));
            $block = "<a name=\"$safeName\"></a><a href=\"#$safeName\" class=\"title\"><h$level$attr>" . $this->runSpanGamut($matches[1]) . "</h$level></a>";
        }
        else
        {
            $block = "<h$level$attr>" . $this->runSpanGamut($matches[1]) . "</h$level>";
        }

        return "\n" . $this->hashBlock($block) . "\n\n";
    }

    protected function _doHeaders_callback_atx($matches)
    {
        $level = strlen($matches[1]);
        $defaultId = is_callable($this->header_id_func) ? call_user_func($this->header_id_func, $matches[2]) : null;
        $attr = $this->doExtraAttributes("h$level", $dummy =& $matches[3], $defaultId);

        if ($level == 2)
        {
            $safeName = Str::slug($this->runSpanGamut($matches[2]));
            $block = "<a name=\"$safeName\"></a><a href=\"#$safeName\" class=\"title\"><h$level$attr>" . $this->runSpanGamut($matches[2]) . "</h$level></a>";
        }
        else
        {
            $block = "<h$level$attr>" . $this->runSpanGamut($matches[2]) . "</h$level>";
        }

        return "\n" . $this->hashBlock($block) . "\n\n";
    }
}