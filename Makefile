COV=coverage
TEST=tests

all: 
	@echo "Targets:"
	@echo "\ttest -- Run the unit test suites. Arguments may be specified at run time."
	@echo "\t\t example 1: make test"
	@echo "\t\t example 2: make test ARGS=\"--filter FILTER_CRITERIA\""
	@echo ""
	@echo "\ttest_coverage -- Create a unit test coverage report in coverage/"
	@echo ""


test:
	@cd $(TEST) && ./runTests.sh ${ARGS}

test_coverage: mkdir_coverage
	@cd $(TEST) && ./runTests.sh --coverage-html ../$(COV)

mkdir_coverage:
	@mkdir -p $(COV)

