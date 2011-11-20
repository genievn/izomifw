<?php
class izAction extends Object {
	public function getContent(){
		if (!$this->content && $this->getObject() instanceof izRender)
		{
			//If content is not available then get the renderer
			return $this->getObject()->render();
		}
		return $this->content;
	}
}
?>