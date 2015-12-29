.PHONY: deploy

deploy:
	# as1 server
	rsync -atvhz --exclude=".*/" --exclude="bower_components/" --exclude="node_modules/" --exclude="tests/" ../better-css-delivery pt:plugins/
