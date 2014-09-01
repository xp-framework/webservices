GENERATOR?= 'echo "Pass GENERATOR variable"'

all: src/main/php/webservices/json/JsonParser.class.php

clean:
	-rm y.output

src/main/php/webservices/json/JsonParser.class.php: src/main/jay/json.jay
	@echo "===> Generating parser"
	@$(GENERATOR) src/main/jay/json.jay php5-ns Json > src/main/php/webservices/json/JsonParser.class.php

.PHONY: $(PHONY)
